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

// can_user_edit the parent section

// parent id must come from passed var 

if (!$section_array = get_section($parent_id)){
	// no such section
	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
    exit();  	
} else {
// section exists 
	if(!can_user_edit_section($user_array, $section_array)){
    	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=$section_array[section_id]");
	    exit();
    }
     
// at this point the current user can the parent section
  
 
 // this is just a simple username look up
  
  if(!$moderator_name = get_username($section_array[user_id]))
  	nexus_error();
  
 $breadcrumbs = get_breadcrumbs_topic($section_array[section_id]);
#  	nexus_error();
	
  // show header
  // change page template if new messages are waiting
   if(get_count_unread_messages($_SESSION[current_id])>0){
       $t->set_file("header","mail_page.html");
  } else {
       $t->set_file("header","page.html");
  }
 
  

  if ($num_msg = count_instant_messages($_SESSION[current_id])){
	$t->set_var("num_msg",$num_msg);
  }else{
	$t->set_var("num_msg","no");
  }

  $t->set_var("pagetitle","Create Menu");
  $t->set_var("breadcrumbs",$breadcrumbs);


  $t->set_var("owner_id",$section_array[user_id]);
  $t->set_var("ownername",$moderator_name);

  $t->set_var("user_name",$user_array["user_name"]);
  $t->set_var("user_popname",$user_array["user_popname"]);
  $t->set_var("user_id",$_SESSION[current_id]);

  $t->set_var("section_id",$topic_array[section_id]);

  $t->pparse("HeaderOutput","header");

  // show modify section comment

  // get create section template
  // fill in section_id
  $t->set_file("sectionform","create_section.html");
  
  
  $t->set_var("SECTION_ID",$section_array[section_id]);
  
  // SELECT
  $select_code = '<option value="'.$section_array[user_id].'">'.$moderator_name.'</option>';
  $userlist_array=get_userlist_array();
  foreach ($userlist_array  as $current_element){
	$select_code = $select_code.'<option value="'.$current_element[user_id].'">'.$current_element[user_name].'</option>';
  }
  $t->set_var("SELECT_CODE",$select_code);
  
  $t->pparse("SectionOutput","sectionform");	
	
	
# UPDATE include breadcrumbs and bottom code
}


?>