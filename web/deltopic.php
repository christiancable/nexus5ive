<?php
// new add post code - interface


include('../includes/database.php');

$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$my_theme; 
$t = new Template($template_location);


// check login
if (!validlogin()){
	eject_user();	
}

#echo "here";
if(!$user_array = get_user_array($_SESSION[current_id])){
	nexus_error();
}
#echo "there";

// can_user_edit fuction here

if (!$topic_array = get_topic($topic_id)){
	// no such topic
	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
    exit();  	
} else {
// topic exists 
	if(!can_user_edit_topic($user_array, $topic_array)){
		// topic exists but user can not edit it
    	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=$topic_array[section_id]");
	    exit();
    }
     
// at this point the current user can edit the section 
  
 

 //section owner info
 // this is just a simple username look up
  $section_array = get_section($topic_array[section_id]);

  if(!$moderator_name = get_username($section_array[user_id]))
  	nexus_error();

  if(!$breadcrumbs = get_breadcrumbs_topic($section_array[section_id]))
  	nexus_error();

  // show header
display_header($t,
	       $breadcrumbs,
	       'Delete "'.$topic_array[topic_title].'"',
	       $user_array["user_name"],
	       $user_array["user_popname"],
	       $_SESSION[current_id],
	       count_instant_messages($_SESSION[current_id]),
	       $section_array[user_id],
	       $moderator_name,
	       get_count_unread_comments($_SESSION[current_id]),
	       get_count_unread_messages($_SESSION[current_id]));

  $t->set_file("topicform","deltopic.html");
  
  $messages_in_topic = get_count_topic_messages($topic_array[topic_id]);
  $t->set_var("POST", $messages_in_topic);
  
  $t->set_var("TOPIC_NAME",$topic_array[topic_title]);
  $t->set_var("SECTION_ID",$topic_array[section_id]);

  
  $t->set_var("DESCRIPTION",$topic_array[topic_description]);
  $t->set_var("TOPIC_ID",$topic_array[topic_id]);
  
  
  $t->pparse("TopicOutput","topicform");	
	
page_end($breadcrumbs,$t);
# UPDATE include breadcrumbs and bottom code
}


?>
