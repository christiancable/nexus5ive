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

if(!$user_array = get_user_array($_SESSION[current_id])){
	nexus_error();
}

// can_user_edit fuction here

if (!$topic_array = get_topic($topic_id)){
	// no such topic
	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
    exit();  	
} else {
 
	if(!can_user_add_message($user_array, $topic_array)){
    	header("Location: http://".$_SERVER['HTTP_HOST']."/readtopic.php?section_id=$topic_array[section_id]&topic_id=$topic_array[topic_id]");
	    exit();
    }
     

  
 

 //get topic owner info
 // this is just a simple username look up

  if(!$owner_array = get_topic_owner($topic_id)){
	nexus_error();
  }
  
  $breadcrumbs = get_breadcrumbs_topic($topic_array[section_id]);
  
  
  // show header
  display_header($t,
	       $breadcrumbs,
	       "Adding Comment to ".$topic_array[topic_title],
	       $user_array["user_name"],
	       $user_array["user_popname"],
	       $_SESSION[current_id],
	       count_instant_messages($_SESSION[current_id]),
	       $owner_array[owner_id],
	       $owner_array[owner_name],
	       get_count_unread_comments($_SESSION[current_id]),
	       get_count_unread_messages($_SESSION[current_id]));



  $location_str = '<a href="readtopic.php?section_id='.$topic_array[section_id].'&topic_id='.$topic_array[topic_id].'"><i>Posting</i> '.$topic_array[topic_title].'</a>';
  update_location($location_str);


  // show post comment

  $t->set_file("postform","post.html");
  

  $t->set_var("section_id",$topic_array[section_id]);
  $t->set_var("topic_id",$topic_array[topic_id]);
  // if the topic is read only, remind the user that they have privs to edit here
  if($topic_array[topic_readonly]=='y'){
  	$t->set_var("readonly_hint","<b>Remember:</b> Regular users can not add to this topic<br><br>");
  }else{
   	$t->set_var("readonly_hint","");
  }
 
   // show previous comment if one exists in otherwords is this an empty topic
  
  if($previous_message_id = get_latest_message_id($topic_array[topic_id])){
  	// show previous message
	if($topic_array[topic_annon]=='y') {
		if(is_topic_owner($topic_array[topic_id], $_SESSION[current_id], $db)) {
			// can see
			$display_mode='SECRET_OWNER';
		} else {
			// can not see
			$display_mode='SECRET_COMMENT';
		}

	} else {
		if(is_topic_owner($topic_array[topic_id], $_SESSION[current_id], $db)) {
			// owner
			$display_mode='NORMAL_OWNER';
		} else {
			// not owner
			$display_mode='NORMAL_COMMENT';
		 	$t->set_var("readonly_hint","<b>Remember:</b> Any HTML code you use must be activated by the moderator.<br><br>");
		}

	}
	$t->set_var("reply text","Reply to...");
	$t->pparse("PostOutput","postform");	
	
	display_message($previous_message_id, $_SESSION[current_id], $t, $display_mode, $db);
	
  } else {
    $t->set_var("reply text","");
    $t->pparse("PostOutput","postform");	
  }

# UPDATE include breadcrumbs and bottom code
}


?>
