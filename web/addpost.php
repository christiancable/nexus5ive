<?php
# add post
# backend to post.php
# should do no output to the screen here

include('../includes/theme.php');
include('../includes/database.php');

$db = opendata();
session_start();

$template_location =TEMPLATE_HOME.$my_theme; 
$t = new Template($template_location);


// check login
if (!validlogin()){
	eject_user();	
}

if(!$user_array = get_user_array($_SESSION[current_id])){
	nexus_error();
}

// can_user_edit fuction here

###NEED TO GET THE TOPIC ID FROM SOMEWHERE FIRST!

if (!$topic_array = get_topic($topic_id)){
	// no such topic
	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
    exit();  	
} else {
 
	if(!can_user_add_message($user_array, $topic_array)){
    	header("Location: http://".$_SERVER['HTTP_HOST']."/readtopic.php?section_id=$topic_array[section_id]&topic_id=$topic_array[topic_id]");
	    exit();
    }
     
// if not section owner or sysop strip out any html

	// subject and comment HTML VARS

	$comment = $HTTP_POST_VARS[comment];
	$subject = $HTTP_POST_VARS[subject];
	
	$tempsubject = htmlspecialchars($subject,ENT_QUOTES);
	$subject = nl2br($tempsubject);
	
	if( is_section_owner($topic_array[section_id],$user_array[user_id],$db) )  {
		// if we have the privs use html   
	   $comment = nl2br($comment);
	} else {
	  // strip out html replace line endings
	   $tempmessage = htmlspecialchars($comment, ENT_QUOTES);
	   $comment = nl2br($tempmessage);
	}

	
	
	// add post
	// array of the format, should I make a blank array somehow here?????
	
	$message_array[message_text]=$comment;
	$message_array[topic_id]=$topic_array[topic_id];
	$message_array[user_id]=$user_array[user_id];
	$message_array[message_title]=$subject;
	$message_array[message_popname]=$user_array[user_popname];
	
	if(!add_message($message_array)){
		nexus_error();
	}
	// update number of edits

	inc_total_edits($user_array);
 
	// redirect to readtopic
	header("Location: http://".$_SERVER['HTTP_HOST']."/readtopic.php?section_id=$topic_array[section_id]&topic_id=$topic_array[topic_id]");
	exit();

}
?>