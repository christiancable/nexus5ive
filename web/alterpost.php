<?php
/**********************************************/
/* alterpost.php                              */
/*                                            */
/* edit an existing post for use by moderator */
/**********************************************/

// includes
include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

// parameters
$message_id=$_GET['message_id'];
$topic_id=$_GET['topic_id'];

$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION['my_theme']; 
$t = new Template($template_location);


// check login
if (!validlogin())
{
  eject_user();	
}

if(!$user_array = get_user_array($_SESSION['current_id']))
{
  show_error($_SERVER['PHP_SELF']."get_user_array failed<br>SESSION is ".
	     print_r($_SESSION,true));
}

if (!$message_array = get_message_with_time($message_id))
{
  // check to see if the message exists 
  
  // no such message
  header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().
	 "section.php?section_id=1");
  exit();  	
}
else
{
  // topic exists 
  // get the topic id here
  if(!$topic_array = get_topic($message_array['topic_id']))
    {
      header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().
	     "section.php?section_id=1");
      exit();  	
    }	
  

# can user edit post

if (!$section_array = get_section($topic_array['section_id']))
    {
      nexus_error();
    }

  if (!can_user_edit_post(
			 $user_array['user_id'],
			 $user_array['user_sysop'],
			 $section_array['user_id'],
			 $message_array['user_id'],
			 $message_array['message_time']
			 ))



    {
      // topic exists but user can not edit it
      header("Location: http://".$_SERVER['HTTP_HOST'].
	     "/section.php?section_id=$topic_array[section_id]");
      exit();
    }

  // at this point the current user can edit the message


# can user edit post end
  
  // at this point the current user can edit the message
  
  //section owner info
  // this is just a simple username look up
  $section_array = get_section($topic_array['section_id']);
  
  if(!$moderator_name = get_username($section_array['user_id']))
    {
      show_error($_SERVER['PHP_SELF'].
		 "get_user_array failed for moderator <br> SESSION is ".
		 print_r($_SESSION,true));
    }
  
  if(!$breadcrumbs = get_breadcrumbs_topic($section_array['section_id']))
    {
      show_error($_SERVER['PHP_SELF']."get_breadcrumbs_topic failed <br>".
		 print_r($section_array, true));
    }
  
  // show header
  display_header($t,
		 $breadcrumbs,
		 "Alter Post in ... ".$topic_array['topic_title'],
		 $user_array['user_name'],
		 $user_array['user_popname'],
		 $_SESSION['current_id'],
		 count_instant_messages($_SESSION['current_id']),
		 $section_array['user_id'],
		 $moderator_name,
		 get_count_unread_comments($_SESSION['current_id']),
		 get_count_unread_messages($_SESSION['current_id']));  

  display_navigationBar(
			$topicleap=true,
			$whosonline=true,
			$mainmenu=false,
			$examineuser=true,
			$returntosection=false,
			
			$createtopic=false,
			$createmenu=false,
			$postcomment=false,
			
			$section_id=false,
			$parent_id=false,
			$topic_id=false
			);
    
  // message  
  $t->set_file("topicform","alterpost.html");
  if(!$post_username = get_username($message_array['user_id']))
    {
      $post_username = "unknown";
    }
  
  $t->set_var("message_username",$post_username);	
  $t->set_var("message_user_id",$message_array['user_id']);
  $t->set_var("message_popname",$message_array['message_popname']);
  $t->set_var("subject", $message_array['message_title']);
  $t->set_var("date",$message_array['format_time']);
  $t->set_var("message",$message_array['message_text']);
  $t->set_var("section_id",$topic_array['section_id']);
  $t->set_var("topic_id", $topic_array['topic_id']);
  $t->set_var("message_id", $message_id);
  
  $t->pparse("TopicOutput","topicform");	
  display_navigationBar(
			$topicleap=true,
			$whosonline=true,
			$mainmenu=false,
			$examineuser=true,
			$returntosection=false,
			
			$createtopic=false,
			$createmenu=false,
			$postcomment=false,
			
			$section_id=false,
			$parent_id=false,
			$topic_id=false
			);

  page_end($breadcrumbs, $t);
}
?>
