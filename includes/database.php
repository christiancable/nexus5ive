<?php
// non-visual functions
// all database bits will eventuall move to database_layer.php
// env

// will change the name of this to nexuslib.php I think - cfc

putenv("TZ=GB");

// needed libs
include('../phplib/php/template.inc');
include('database_layer.php');
include('interface_layer.php');
include('site.php');

// defines
define('TEMPLATE_HOME', '../templates/');

// functions
function opendata()
{
	//return a connection handle to the database
	
	$db = mysql_pconnect(MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD);
	mysql_select_db(MYSQL_DATABASE, $db);

	return $db;
} 

function validlogin()
{
	// to check to see if there is a valid current_id session var
	if (session_is_registered("current_id")) {

		$db = opendata();
		global $current_id;

		$sql = "DELETE FROM whoison WHERE user_id=$current_id";
		mysql_query($sql, $db);
		$sql = "INSERT INTO whoison (user_id) VALUES ('$current_id')";
		mysql_query($sql, $db);

		return true;
	} else {
		// echo "Invalid Login";
		return false;
	} 
} 

function is_sysop($user_id)
{
	$sql = "SELECT user_sysop FROM usertable where user_id=$user_id";
	if ($user_id != null) {
		$sysopinfo = mysql_query($sql);
		$sysopresult = mysql_result($sysopinfo, 0, "user_sysop");
		if ($sysopresult == 'y') {
			return true;
		} else {
			return false;
		} 
	} else {
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

	if (($owner == $user_id) or is_sysop($user_id)) {
		return true;
	} else {
		return false;
	} 
} 

function is_section_owner($section_id, $user_id, $db)
{
	$sql = "SELECT user_id FROM sectiontable
           WHERE section_id=$section_id";

	$ownerinfo = mysql_query($sql);
	$owner = mysql_result($ownerinfo, 0, "user_id");

	if (($owner == $user_id) or is_sysop($user_id)) {
		return true;
	} else {
		return false;
	} 
} 

function get_username($user_id)
{
	$sql = "SELECT user_name FROM usertable WHERE user_id=$user_id";

	if(!$usernameinfo = mysql_query($sql)){
		return false;
	}

	if(mysql_num_rows($usernameinfo)){
		$userresult = mysql_result($usernameinfo, 0, "user_name");
		return $userresult;
	} else {
		return false;
	}


} 


function count_instant_messages($user_id)
{
    // returns the number of instant messages
	
	$sql = "SELECT nexusmessage_id FROM nexusmessagetable WHERE user_id=$user_id";

	if (!$nexusmessageinfo = mysql_query($sql)) {
		nexus_error();
	} 

	if ($num_msg = mysql_num_rows($nexusmessageinfo)) {
		return $num_msg;
	} else {
		return false;
	} 
} 

function count_messages_in_topic($topic_id)
{
	// returns the total amount of messages in a given topic
	// returns false if any probs

	$sql = "SELECT COUNT(message_id) AS total_msg FROM messagetable WHERE topic_id=$topic_id";

	if (!$message_info = mysql_query($sql)) {
		return false;
	}

	if (mysql_num_rows($message_info)) {
		$message_array = mysql_fetch_array($message_info, MYSQL_ASSOC);
		return $message_array[total_msg];
	} else {
		return false;
	}
}


function nexus_error()
{
	/**
	 * displays a meaninful error message
	 * 
	 * update nov 17 2001 - added session_start
	 * update nov 2nd 2001 - xian
	 */

	/**
	 * $template_location = TEMPLATE_HOME.DEFAULT_TEMPLATE; 
	 * 
	 * 
	 * 
	 * $t = new Template($template_location);
	 * $t->set_file("MyFileHandle", "page.html");
	 * 
	 * $t->set_var("pagetitle","Error");
	 * $error_message =  "<br>Sorry, something has gone all wrong. Life is a bit like that sometimes.<br>";
	 * $error_message .= "Please make a note of the message below and tell <a href=\"mailto:".SYSOP_MAIL."\">";
	 * $error_message .= ."</a><br><br>";
	 * $error_message .= "ERROR: ".mysql_errno()." ".mysql_error();
	 * $error_message .= '<br><br><a href="index.php">Restart Nexus</a>';
	 * $t->set_var("content",$error_message);
	 * session_start();
	 * session_destroy();
	 * $t->parse("MyOutput","MyFileHandle");
	 * $t->p("MyOutput");
	 * exit();
	 */
	eject_user();
} 

function update_location($location)
{
	/**
	 * updates user table with location of user passed via location arg
	 * 
	 * update nov 3 2001 - xian
	 */
	global $current_id;
	$location = mysql_real_escape_string($location);
	$sql = "UPDATE usertable SET user_location='" . $location . "' WHERE user_id=$current_id";
#		echo "< !-- debug update_location is $sql -- >";
	if (!$sql_result = mysql_query($sql)) {

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
	if (!unsubscribed_from_topic($topic_id, $user_id)) {
		// ###
		$sql = "SELECT msg_date FROM topicview WHERE user_id=$user_id AND topic_id=$topic_id LIMIT 1";

		if (!$last_message_result = mysql_query($sql)) {
			nexus_error();
		} 

		if (mysql_num_rows($last_message_result)) {
			$last_message = mysql_fetch_array($last_message_result);

			$sql = "SELECT COUNT(messagetable.message_id) AS new_msg_count FROM messagetable  WHERE topic_id=$topic_id AND message_time > " . $last_message["msg_date"];

			if (!$new_message_result = mysql_query($sql)) {
				nexus_error();
			} 
			$new_messages_array = mysql_fetch_array($new_message_result);
			$new_messages = $new_messages_array[new_msg_count];
		} else { // never read, check to see if there are any messages
			if (count_messages_in_topic($topic_id)) {
				$new_messages = true;
			} else {
				$new_messages = false;
			} 
		} 
		// ##
	} else {
		$new_messages = false;
	} 

	return $new_messages;
} 

#

function nx_code($text){

	# regular expressions ROX	
	# can we check for http here somehow? - cfc
	
	$pattern ="/\[PICTURE\-\](.*)\[\-PICTURE\]/Ui";

	# check to see if the user has turned off pictures here 
	#if user has pictures turned off
	
	if($_SESSION[no_pictures]<>'n') {
		$replacement = '<a href="'."$1".'" target="_blank">[Click Here To See '."$1".']</a>';	    
	} else {
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
	
	$pattern ="#\[ASCII\-\](.+?)\[\-ASCII\]#isU";
	$replacement = '<pre> '."$1".'</pre>';
    $text = preg_replace($pattern, $replacement, $text);
	
	return $text;
	
	
}


function get_section_parent_info($section_id)
{ 
	// returns info about the parent section if any
	$sql = "SELECT section_id, section_title, parent_id FROM sectiontable WHERE section_id = $section_id";

	if (!$section_info = mysql_query($sql)) {
		return false;
	} 

	if (mysql_num_rows($section_info)) {
		$section_result = mysql_fetch_array($section_info, MYSQL_ASSOC);
		return $section_result;
	} else {
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
	$breadcrumb_array[$count]["section_id"] = $current_breadcrumb["section_id"];
	$breadcrumb_array[$count]["section_title"] = $current_breadcrumb["section_title"];
	$count++; 
	// echo "then onto topic ".$current_crumb["topic_topic_id"]."<br>";
	while ($current_breadcrumb = get_section_parent_info($current_breadcrumb["parent_id"])) {
		$breadcrumb_array[$count]["section_id"] = $current_breadcrumb["section_id"];
		$breadcrumb_array[$count]["section_title"] = $current_breadcrumb["section_title"];
		$count++;
	} 

	if ($count) {
		return array_reverse($breadcrumb_array);
	} else {
		return false;
	} 
} 

function get_breadcrumbs($section)
{
	// update this when newsection is real
	$breadcrumbs = get_section_parents($section);
	$num_of_crumbs = count($breadcrumbs);

	for($loop_count = 0; $loop_count < $num_of_crumbs-1; $loop_count++) {
		$crumb_urls .= '<a href="section.php?section_id=' . $breadcrumbs[$loop_count]["section_id"] . '">';
		if(strlen($breadcrumbs[$loop_count]["section_title"]))
			$crumb_urls .= $breadcrumbs[$loop_count]["section_title"] . "</a> -&gt; ";
		else
			$crumb_urls .= " - " . "</a> -&gt; ";
	} 
	return $crumb_urls;
} 

function get_breadcrumbs_topic($section)
{
	// update this when newsection is real
	$breadcrumbs = get_section_parents($section);
	$num_of_crumbs = count($breadcrumbs);

	for($loop_count = 0; $loop_count < $num_of_crumbs; $loop_count++) {
		$crumb_urls .= '<a href="section.php?section_id=' . $breadcrumbs[$loop_count]["section_id"] . '">';
		if(strlen($breadcrumbs[$loop_count]["section_title"]))
			$crumb_urls .= $breadcrumbs[$loop_count]["section_title"] . "</a> -&gt; ";
		else
			$crumb_urls .= " - " . "</a> -&gt; ";
	} 
	return $crumb_urls;
} 

function mkPasswd() {
// +----------------------------------------------------------------------+
// | PHP Pronounceable Password Generator                                 |
// +----------------------------------------------------------------------+
// | Author: Max Dobbie-Holman <max@blueroo.net>                          |
// +----------------------------------------------------------------------+
//
// View the demo at http://www.blueroo.net/max/pwdgen.php
/**
 * Generates an 8 character pronounceable password.
 *
 * @author        Max Dobbie-Holman <max@blueroo.net>
 * @version       1.0
 */

// using GPL password function, copyright above	

	$consts='bcdgklmnprst';
	$vowels='aeiou';
	
	for ($x=0; $x < 6; $x++) {
		mt_srand ((double) microtime() * 1000000);
	$const[$x] = substr($consts,mt_rand(0,strlen($consts)-1),1);
	$vow[$x] = substr($vowels,mt_rand(0,strlen($vowels)-1),1);
	}
	
	return $const[0] . $vow[0] .$const[2] . $const[1] . $vow[1] . $const[3] . $vow[3] . $const[4];
}


/* ======================================================================= 
    
ifsnow's email valid check function SnowCheckMail Ver 0.1 
   
funtion SnowCheckMail ($Email,$Debug=false) 

$Email : E-Mail address to check. 
$Debug : Variable for debugging. 

* Can use everybody if use without changing the name of function. 

Reference : O'REILLY - Internet Email Programming 

HOMEPAGE : http://www.hellophp.com 

ifsnow is korean phper. Is sorry to be unskillful to English. *^^*;; 

========================================================================= */ 

function SnowCheckMail($Email,$Debug=false) 
{ 
    global $HTTP_HOST; 
    $Return =array();   
    // Variable for return. 
    // $Return[0] : [true|false] 
    // $Return[1] : Processing result save. 

    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $Email)) { 
        $Return[0]=false; 
        if ($Debug) echo "Error : {$Email} is E-Mail form that is not right.<br>";          
        return false; 
    } 
    else if ($Debug) echo "Confirmation : {$Email} is E-Mail form that is not right.<br>"; 

    list ( $Username, $Domain ) = split ("@",$Email); 

    if ( checkdnsrr ( $Domain, "MX" ) )  { 
        if($Debug) echo "Confirmation : MX record about {$Domain} exists.<br>"; 
        if ( getmxrr ($Domain, $MXHost))  { 
      if($Debug) { 
                echo "Confirmation : Is confirming address by MX LOOKUP.<br>"; 
              for ( $i = 0,$j = 1; $i < count ( $MXHost ); $i++,$j++ ) { 
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result($j) - $MXHost[$i]<BR>";   
        } 
            } 
        } 
        $ConnectAddress = $MXHost[0]; 
    } 
    else { 
        // If there is no MX record simply @ to next time address socket connection do . 
        $ConnectAddress = $Domain;          
        if ($Debug) echo "Confirmation : MX record about {$Domain} does not exist.<br>"; 
    } 

    $Connect = fsockopen ( $ConnectAddress, 25 ); 

    if ($Connect)    
    { 
        if ($Debug) echo "Connection succeeded to {$ConnectAddress} SMTP.<br>"; 
        if ( ereg ( "^220", $Out = fgets ( $Connect, 1024 ) ) ) { 
              
            fputs ( $Connect, "HELO $HTTP_HOST\r\n" ); 
                if ($Debug) echo "Run : HELO $HTTP_HOST<br>"; 
            $Out = fgets ( $Connect, 1024 ); // Receive server's answering cord. 

            fputs ( $Connect, "MAIL FROM: <{$Email}>\r\n" ); 
                if ($Debug) echo "Run : MAIL FROM: &lt;{$Email}&gt;<br>"; 
            $From = fgets ( $Connect, 1024 ); // Receive server's answering cord. 

            fputs ( $Connect, "RCPT TO: <{$Email}>\r\n" ); 
                if ($Debug) echo "Run : RCPT TO: &lt;{$Email}&gt;<br>"; 
            $To = fgets ( $Connect, 1024 ); // Receive server's answering cord. 

            fputs ( $Connect, "QUIT\r\n"); 
                if ($Debug) echo "Run : QUIT<br>"; 

            fclose($Connect); 

                if ( !ereg ( "^250", $From ) || !ereg ( "^250", $To )) { 
                    $Return[0]=false; 
                    if ($Debug) echo "{$Email} is address done not admit in E-Mail server.<br>"; 
                    return false; 
                } 
        } 
    } 
    else { 
        $Return[0]=false; 
        if ($Debug) echo "Can not connect E-Mail server ({$ConnectAddress}).<br>"; 
        return false; 
    } 
    $Return[0]=true; 
    return true; 
} 
?>
