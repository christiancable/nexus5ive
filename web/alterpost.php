<?php
// alter existing post - cfc


include('../includes/database.php');

// parameters
$message_id=$_GET[message_id];
$topic_id=$_GET[topic_id];

$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION[my_theme]; 
$t = new Template($template_location);


// check login
if (!validlogin()){
	eject_user();	
}

if(!$user_array = get_user_array($_SESSION[current_id])){
	nexus_error();
}

if (!$message_array = get_message_with_time($message_id)){
	// check to see if the message exists 
	// no such message
	header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");
    exit();  	
} else {
// topic exists 
	// get the topic id here
	if(!$topic_array = get_topic($message_array[topic_id])){
		header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");
	    	exit();  	
	}	
	if(!can_user_edit_topic($user_array, $topic_array)){
		// topic exists but user can not edit it
    		header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=$topic_array[section_id]");
	    	exit();
    }
     
// at this point the current user can edit the message

 //section owner info
 // this is just a simple username look up
  $section_array = get_section($topic_array[section_id]);

  if(!$moderator_name = get_username($section_array[user_id]))
  	nexus_error();

  if(!$breadcrumbs = get_breadcrumbs_topic($section_array[section_id]))
  	nexus_error();

  // show header
  ## header

display_header($t,
	       $breadcrumbs,
	       "Alter Post in ... ".$topic_array[topic_title],
	       $user_array["user_name"],
	       $user_array["user_popname"],
	       $_SESSION[current_id],
	       count_instant_messages($_SESSION[current_id]),
	       $section_array[user_id],
	       $moderator_name,
	       get_count_unread_comments($_SESSION[current_id]),
	       get_count_unread_messages($_SESSION[current_id]));  
  ## message

  $t->set_file("topicform","alterpost.html");
  if(!$post_username = get_username($message_array[user_id]))
  	 $post_username = "unknown";
	 
  $t->set_var("message_username",$post_username);	
  $t->set_var("message_user_id",$message_array[user_id]);
  $t->set_var("message_popname",$message_array[message_popname]);
  $t->set_var("subject", $message_array[message_title]);
  
  $t->set_var("date",$message_array[format_time]);
#  $t->set_var("date","today is the day");
  $t->set_var("message",ereg_replace("<br />","",$message_array[message_text]));
  
  $t->set_var("section_id",$topic_array[section_id]);
  $t->set_var("topic_id", $topic_array[topic_id]);
  $t->set_var("message_id", $message_id);
  
  $t->pparse("TopicOutput","topicform");	
	
page_end($breadcrumbs, $t);
# UPDATE include breadcrumbs and bottom code
}


?>
