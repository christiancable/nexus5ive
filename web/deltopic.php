<?php
// new add post code - interface

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
  // change page template if new messages are waiting
	if (get_count_unread_messages($_SESSION[current_id])>0) {
        $t->set_file("header","mail_page.html");
	} else {
       $t->set_file("header","page.html");
	}

  if ($num_msg = count_instant_messages($_SESSION[current_id])){
	$t->set_var("num_msg",$num_msg);
  }else{
	$t->set_var("num_msg","no");
  }

  
  
  $t->set_var("pagetitle",'Delete "'.$topic_array[topic_title].'"');
  $t->set_var("breadcrumbs",$breadcrumbs);


  $t->set_var("owner_id",$section_array[user_id]);
  $t->set_var("ownername",$moderator_name);

  $t->set_var("user_name",$user_array["user_name"]);
  $t->set_var("user_popname",$user_array["user_popname"]);
  $t->set_var("user_id",$_SESSION[current_id]);

  $t->set_var("section_id",$section_array[section_id]);
  
  $t->pparse("HeaderOutput","header");

  $t->set_file("topicform","deltopic.html");
  
  $messages_in_topic = get_count_topic_messages($topic_array[topic_id]);
  $t->set_var("POST", $messages_in_topic);
  
  $t->set_var("TOPIC_NAME",$topic_array[topic_title]);
  $t->set_var("SECTION_ID",$topic_array[section_id]);

  
  $t->set_var("DESCRIPTION",$topic_array[topic_desctiption]);
  $t->set_var("TOPIC_ID",$topic_array[topic_id]);
  
  
  $t->pparse("TopicOutput","topicform");	
	
page_end($breadcrumbs);
# UPDATE include breadcrumbs and bottom code
}


?>
