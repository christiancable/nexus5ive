<?php
// new add post code - interface


include('../includes/database.php');

$db = opendata();
session_start();

// check login
if (!validlogin()){
	eject_user();	
}

if(!$user_array = get_user_array($_SESSION[current_id])){
	nexus_error();
}

// can_user_edit fuction here

if (!$section_array = get_section($section_id)){
	// no such section
	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
    exit();  	
} else {
// section exists 
	if(!can_user_edit_section($user_array, $section_array)){
    	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=$section_array[section_id]");
	    exit();
    }
     
// at this point the current user can edit the section 
  
$section_array[section_title] = htmlspecialchars($HTTP_POST_VARS[title], ENT_QUOTES);
$section_array[user_id] = $HTTP_POST_VARS[moderator];
$section_array[section_weight] = $HTTP_POST_VARS[weight];
$section_array[section_intro] = htmlspecialchars($HTTP_POST_VARS[description], ENT_QUOTES);

if(update_section($section_array)){
	//worked
} else {
	//failed
	nexus_error();
}


 
 // redirect to sections parent
header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=$section_array[parent_id]");
}


?>