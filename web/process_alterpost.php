<?php
// alter existing post - cfc

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

if (!$message_array = get_message($message_id)){
	// check to see if the message exists 
	// no such message
	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
    exit();  	
} else {
// topic exists 
	// get the topic id here
	if(!$topic_array = get_topic($message_array[topic_id])){
		header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
	    	exit();  	
	}	
	if(!can_user_edit_topic($user_array, $topic_array)){
		// topic exists but user can not edit it
    		header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=$topic_array[section_id]");
	    	exit();
    }
     
// at this point the current user can edit the message

	
	$comment = $HTTP_POST_VARS[comment];
	$subject = $HTTP_POST_VARS[subject];
	
	$tempsubject = htmlspecialchars($subject,ENT_QUOTES);
	$subject = nl2br($tempsubject);
	
	if($HTTP_POST_VARS[allowhtml])  {
	   $comment = nl2br($comment);
	} else {
	   $tempmessage = htmlspecialchars($comment, ENT_QUOTES);
	   $comment = nl2br($tempmessage);
	}
	$message_array[text]=$comment;
	$message_array[message_title]=$subject;
	
	# escape the popname here
	$message_array[message_popname]=addslashes($message_array[message_popname]);
	#addslashes($popname);
	//unset the time
	unset ($message_array[message_time]);
	// update 
	if(update_message($message_array)){
		//worked		
	} else {

		echo "broken - copy this page to xian<br>";
		echo "message_text=$message_array[text]<br>";
 		echo "topic_id=$message_array[topic_id]<br>";
		echo "user_id=$message_array[user_id]";
		echo "<br>message_title=$message_array[message_title]";
		echo "<br>message_time=$message_array[message_time]";
		echo "<br>message_popname=$message_array[message_popname]";
		echo "<br>message_id=$message_array[message_id]";
#		nexus_error();
	}

	header("Location: http://".$_SERVER['HTTP_HOST']."/readtopic.php?section_id=$topic_array[section_id]&topic_id=$topic_array[topic_id]"); 	
	exit();
}


?>
