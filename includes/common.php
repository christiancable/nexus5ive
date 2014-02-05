<?php
  // general functions

  // needed libs
include('../phplib/php/template.inc');


// error reporting settings - 20091008

error_reporting(E_ALL | E_STRICT);
ini_set("display_errors", "off");
ini_set("log_errors", "on");
ini_set("error_log", "/home/fraggle/nexus/logs/nexus-php-error.txt");

// functions



function opendata()
{
  // return a connection handle to the database 
  // return the handle if okay or false if not
  
  $return_value = false; 
  
  if ($db = @mysql_pconnect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD))
    {
      mysql_select_db(MYSQL_DATABASE, $db);
      $return_value = $db;
    }
  else
    {
      $return_value = false;
    }
  
  return $return_value;
  
} 



function validlogin()
{
  // to check to see if there is a valid current_id session var
  if (session_is_registered('current_id'))
    {
      
      $db = opendata();
      $current_id = $_SESSION['current_id'];
      
      $sql = "DELETE FROM whoison WHERE user_id=$current_id";
      mysql_query($sql, $db);
      $sql = "INSERT INTO whoison (user_id) VALUES ($current_id)";
      mysql_query($sql, $db);
      
      return true;
    }
  else
    {
      // echo "Invalid Login";
      /* if we have a valid cookie then log the user in as above 

       */
 
      return false;
    } 
} 



function is_sysop($user_id)
{
  $sql = "SELECT user_sysop FROM usertable where user_id=$user_id";
  if ($user_id != null)
    {
      $sysopinfo = mysql_query($sql);
      $sysopresult = mysql_result($sysopinfo, 0, "user_sysop");
      if ($sysopresult == 'y') {
	return true;
      }
      else
	{
	  return false;
	} 
    }
  else
    {
      return false;
    } 
} 



function is_topic_owner($topic_id, $user_id, $db)
{
  $sql = "SELECT sectiontable.user_id FROM topictable,sectiontable
           WHERE topictable.topic_id=$topic_id AND
           sectiontable.section_id=topictable.section_id";
  
  $ownerinfo = mysql_query($sql);
  $owner = mysql_result($ownerinfo, 0, "user_id");
  
  if (($owner == $user_id) or is_sysop($user_id))
    {
      return true;
    }
  else
    {
      return false;
    } 
} 



function is_section_owner($section_id, $user_id, $db)
{
  $sql = "SELECT user_id FROM sectiontable
           WHERE section_id=$section_id";
  
  $ownerinfo = mysql_query($sql);
  $owner = mysql_result($ownerinfo, 0, "user_id");
  
  if (($owner == $user_id) or is_sysop($user_id))
    {
      return true;
    }
  else
    {
      return false;
    } 
} 



function get_username($user_id)
{
  $sql = "SELECT user_name FROM usertable WHERE user_id=$user_id";
  
  if(!$usernameinfo = mysql_query($sql))
    {
      return false;
    }
  
  if(mysql_num_rows($usernameinfo))
    {
      $userresult = mysql_result($usernameinfo, 0, "user_name");
      return $userresult;
    }
  else
    {
      return false;
    }    
}



function count_instant_messages($user_id)
{
  // returns the number of instant messages
  
  $sql = "SELECT nexusmessage_id FROM nexusmessagetable WHERE user_id=$user_id";
  
  if (!$nexusmessageinfo = mysql_query($sql))
    {
      nexus_error();
    } 
  
  if ($num_msg = mysql_num_rows($nexusmessageinfo))
    {
      return $num_msg;
    }
  else
    {
      return false;
    } 
} 



function count_messages_in_topic($topic_id)
{
  // returns the total amount of messages in a given topic
  // returns false if any probs
  
  $sql = "SELECT COUNT(message_id) AS total_msg FROM messagetable WHERE topic_id=$topic_id";
  
  if (!$message_info = mysql_query($sql))
    {
      return false;
    }
  
  if (mysql_num_rows($message_info))
    {
      $message_array = mysql_fetch_array($message_info, MYSQL_ASSOC);
      return $message_array['total_msg'];
    }
  else
    {
      return false;
    }
}



function nexus_error($info = "")
{  
  if ($info)
  {
  	echo "$info";
  }
  eject_user();
} 



function eject_user()
{
  /*
   generic timeout error, consider combining this with show_error function
  */
  header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."timeout.html");
  exit(); 
}



function show_error($function, $message)
{
  
# display a brief error message to the user
  $error_txt =  "<h1>Problem Report</h1>";
  $error_txt = $error_txt . "error in <b>$function</b><br/>";
  $error_txt = $error_txt .  "message is <b>$message</b><br/>";
  $error_txt = $error_txt .  'please report this message <a href="mailto:'.SYSOP_MAIL.'">here</a>';
  $error_txt = $error_txt . '<br/><br/>';
  $error_txt = $error_txt .  '<a href="http://'.$_SERVER['HTTP_HOST'].get_bbsroot().'">Restart</a>';
  echo $error_txt;
  
#email the error to the sysop if the ERROR_MAIL flag is set
  if(ERROR_MAIL)
    {
      mail(SYSOP_MAIL, "Error Report", $error_txt, "From: ".SYSOP_MAIL);
    }
  exit();
}



function update_location($location)
{
  /**
   * updates user table with location of user passed via location arg
   * 
   * update nov 3 2001 - xian
   */
  $current_id = $_SESSION['current_id'];
  $location = mysql_real_escape_string($location);
  $sql = "UPDATE usertable SET user_location='" . $location . "' WHERE user_id=$current_id";
  if (!$sql_result = mysql_query($sql))
    {
      nexus_error();
    }   
  update_last_time_on($current_id);
} 



function new_messages_in_topic($topic_id, $user_id)
{
  /**
   * takes a user_id and topic_id and returns the number of unread messages
   * else false is returned
   */ 
  // check to see if unsubbed
  
  if (!unsubscribed_from_topic($topic_id, $user_id))
    {
      // ###
      
      $sql = "SELECT msg_date FROM topicview WHERE user_id=$user_id AND topic_id=$topic_id LIMIT 1";
      if (!$last_message_result = mysql_query($sql))
	  {
	   nexus_error();
	  } 
      
      if (mysql_num_rows($last_message_result))
	{
	  $last_message = mysql_fetch_array($last_message_result);

	 // added quotes for msg_date - june 2010
	  $sql = 'SELECT COUNT(messagetable.message_id) AS new_msg_count FROM messagetable 
                  WHERE topic_id='.$topic_id.' AND message_time > "'.$last_message['msg_date'].'"';
	  
	  if (!$new_message_result = mysql_query($sql))
	    {
 
	      nexus_error(print_r($sql, $return=true));
	     
	    } 
	  $new_messages_array = mysql_fetch_array($new_message_result);
	  $new_messages = $new_messages_array['new_msg_count'];
	}
      else
	{ // never read, check to see if there are any messages
	  if (count_messages_in_topic($topic_id))
	    {
	      $new_messages = true;
	    }
	  else
	    {
	      $new_messages = false;
	    } 
	} 
    }
  else
    {
      $new_messages = false;
    }   
 
  return $new_messages;
} 



function nx_code($text)
{
  
  // this function gets the data ready from printing on the screen via html

  if($_SESSION['no_pictures']<>'n')
    {
      
      $pattern = '/<\s*img\s*src\s*=\s*\"(.*?)".*?>/i';	
      $replacement = '[PICTURE-]$1[-PICTURE]';	    
      $text = preg_replace($pattern, $replacement, $text); 

      $pattern = '#<a href="(.*)">\[PICTURE-\](.*)\[-PICTURE\]</a>#sSi';
      $replacement = '<a href="$1"><b>[ Link to $1 ]</b></a><br/>[PICTURE-]$2[-PICTURE]';
      $text = preg_replace($pattern, $replacement, $text);
      
      
    }
  
  $pattern ="/\[PICTURE\-\](.*)\[\-PICTURE\]/Ui";
  
  if($_SESSION['no_pictures']<>'n')
    {
      $replacement = '<a href="'."$1".'" target="_blank">[Click Here To See '."$1".']</a>';	    
    }
  else 
    {
      $replacement = '<img src="'."$1".'" alt="'."$1".'">' ;
    }
  $text = preg_replace($pattern, $replacement, $text);
  
  $pattern ="/\[WWW\-\](.*)\[\-WWW\]/Ui";
  $replacement = '<a href="'."$1".'" target="_blank">['."$1".']</a>';
  $text = preg_replace($pattern, $replacement, $text);
  
  $pattern ="#\[I\-\](.*)\[\-I\]#isU";
  $replacement = '<I>'."$1".'</I>';
  $text = preg_replace($pattern, $replacement, $text);
  
  $pattern ="#\[B\-\](.*)\[\-B\]#isU";
  $replacement = '<B>'."$1".'</B>';
  $text = preg_replace($pattern, $replacement, $text);
  
  $pattern ="#\[ASCII\-\](.*)\[\-ASCII\]#isU";
  $replacement = '<pre> '."$1".'</pre>';
  $text = preg_replace($pattern, $replacement, $text);
  
  $pattern ="#\[U\-\](.*)\[\-U\]#isU";
  $replacement = '<u>'."$1".'</u>';
  $text = preg_replace($pattern, $replacement, $text);
  
  $pattern ="#\[SMALL\-\](.*)\[\-SMALL\]#isU";
  $replacement = '<p style="font-size:xx-small">'."$1".'</p>';
  $text = preg_replace($pattern, $replacement, $text);

  $text = nl2br($text);

  // now remove <br /> tags from preformatted blocks

  $pattern ="#\<pre>(.*)</pre>#isU";
  $text = preg_replace_callback($pattern,
				create_function(
						'$matches',
						'return str_replace("<br />","",$matches[0]);'
						),
				$text);

  $pattern ="#\[QUOTE\-\](.*)\[\-QUOTE\]#isU";
  $replacement = '<div class="quote">'."$1".'</div>';
  $text = preg_replace($pattern, $replacement, $text);

  $pattern ="#\[UPDATED\-\](.*)\[\-UPDATED\]#isU";
  $replacement = '<div class="updated">'."$1".'</div>';
  $text = preg_replace($pattern, $replacement, $text);
  
  $pattern ="#\[HUDSON\-\](.*)\[\-HUDSON\]#isU";
  $replacement = '<span class="spoiler">'."$1".'</span>';
  $text = preg_replace($pattern, $replacement, $text);

  $pattern ="#\[SPOILER\-\](.*)\[\-SPOILER\]#isU";
  $replacement = '<span class="spoiler">'."$1".'</span>';
  $text = preg_replace($pattern, $replacement, $text);
  
 # $pattern ="/\[youtube\-\](.*)\[\-youtube\]/Ui";
 # $replacement = '<b>YouTube support coming soon</b><br/><a href="'."$1".'" target="_blank">['."Click Here To Open In A New Window".']</a>';
  #$text = preg_replace($pattern, $replacement, $text);

  
  #$pattern = "#\[youtube\-\]http://(?:www\.)?youtube.com/watch\?v=(.*)\[-youtube\]#isU";
  $pattern = "#\[youtube\-\]http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)\[-youtube\]#im";
  # $pattern ="/\[youtube\-\](.*)\[\-youtube\]/Ui";
  $replacement = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/'."$2".'"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/'."$2".'" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';

  $text = preg_replace($pattern, $replacement, $text);

  return $text;  
}



function get_section_parent_info($section_id)
{ 
  // returns info about the parent section if any
  $sql = "SELECT section_id, section_title, parent_id FROM sectiontable WHERE section_id = $section_id";
  
  if (!$section_info = mysql_query($sql))
    {
      return false;
    } 
  
  if (mysql_num_rows($section_info))
    {
      $section_result = mysql_fetch_array($section_info, MYSQL_ASSOC);
      return $section_result;
    }
  else
    {
      return false;
    } 
} 



function get_section_parents($section_id)
{
  /**
   * IMPUT topic_id
   * OUTPUT assoc array of topic_id and topic_title of all parent topics
   */
  
  $count = 0;
  $current_breadcrumb = get_section_parent_info($section_id);
  $breadcrumb_array[$count]['section_id'] = $current_breadcrumb['section_id'];
  $breadcrumb_array[$count]['section_title'] = $current_breadcrumb['section_title'];
  $count++; 

  while ($current_breadcrumb = get_section_parent_info($current_breadcrumb['parent_id']))
    {
      $breadcrumb_array[$count]['section_id'] = $current_breadcrumb['section_id'];
      $breadcrumb_array[$count]['section_title'] = $current_breadcrumb['section_title'];
      $count++;
    } 
  
  if ($count)
    {
      return array_reverse($breadcrumb_array);
    }
  else
    {
      return false;
    } 
} 



function get_breadcrumbs($section)
{
  // update this when newsection is real
  $crumb_urls ='';
  $breadcrumbs = get_section_parents($section);
  $num_of_crumbs = count($breadcrumbs);
  
  for($loop_count = 0; $loop_count < $num_of_crumbs-1; $loop_count++)
    {
      $crumb_urls .= '<a href="section.php?section_id=' . $breadcrumbs[$loop_count]["section_id"] . '">';
      if(strlen($breadcrumbs[$loop_count]["section_title"]))
	{
	  $crumb_urls .= $breadcrumbs[$loop_count]["section_title"] . "</a> -&gt; ";
	}
      else
	{
	  $crumb_urls .= " - " . "</a> -&gt; ";
	}
    } 
  return $crumb_urls;
} 

function get_breadcrumbs_topic($section)
{
  // update this when newsection is real


  $breadcrumbs = get_section_parents($section);
  $num_of_crumbs = count($breadcrumbs);
  $crumb_urls = "";

  for($loop_count = 0; $loop_count < $num_of_crumbs; $loop_count++) {
    $crumb_urls .= '<a href="section.php?section_id=' . $breadcrumbs[$loop_count]['section_id'] . '">';
    if(strlen($breadcrumbs[$loop_count]["section_title"]))
      {
	$crumb_urls .= $breadcrumbs[$loop_count]["section_title"] . "</a> -&gt; ";
      }
    else
      {
	$crumb_urls .= " - " . "</a> -&gt; ";
      }
  } 
  return $crumb_urls;
} 



function get_bbsroot()
{
  $dirname = dirname($_SERVER['PHP_SELF']);
  if($dirname != "/")
    {      
      $dirname = dirname($_SERVER['PHP_SELF'])."/";
    }
  else
    {
      
    }  
  return $dirname;
}

function send_welcome_email($invalid_user)
{
  // open file
  $welcome_message = file_get_contents("welcome_email.txt");

  // replace vars
  $welcome_message = str_replace("REALNAME",$invalid_user['user_realname'], $welcome_message);
  $welcome_message = str_replace("USERNAME",$invalid_user['user_name'], $welcome_message);
  $welcome_message = str_replace("PASSWORD",$invalid_user['user_password'], $welcome_message);

  mail( $invalid_user['user_email'], BBS_NAME." Account Request",
        $welcome_message, 'From: '.SYSOP_MAIL);
  

  // send email
}

function send_banned_email($banned_user_name)
{
  $email_to = "sysop@nexus5.org.uk";
  $email_from = "From: nexus@nexus5.org.uk";
  $error_txt = " $banned_user_name  attempt from $_SERVER[REMOTE_ADDR]\n";
  $str = "[" . date("Y/m/d h:i:s", mktime()) . "] " . $error_txt;

  mail($email_to, "nexus alert", $str, $email_from);
}

function quote_smart($value)
{
  // Quote variable to make safe
  // use this to prepair strings to be in a query
  // will work regardless of magic quote setting
   
  // see http://uk2.php.net/manual/en/function.mysql-real-escape-string.php
   
  // Stripslashes
  if (get_magic_quotes_gpc()) {
    $value = stripslashes($value);
  }
  // Quote if not integer
  if (!is_numeric($value)) {
    $value = "'" . mysql_real_escape_string($value) . "'";
  }
  return $value;
}

function init_session(){
}
?>
