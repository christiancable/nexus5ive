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

// can_user_edit the parent section

// section id of parent comes from form var
$parent_id = $HTTP_POST_VARS[section_id];

if (!$parent_section_array = get_section($parent_id)){
	// no such section
	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
    exit();  	
} else {
// section exists 
	if(!can_user_edit_section($user_array, $parent_section_array)){
    	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=$parent_section_array[section_id]");
	    exit();
    }
     
// at this point the current user can edit the parent section
  

//get form data
$title = $HTTP_POST_VARS[title];
$description = $HTTP_POST_VARS[description];
$weight = $HTTP_POST_VARS[weight];
$moderator = $HTTP_POST_VARS[moderator];
// does the moderator exist?

// check form data

// blank array to hold the section info
$section_array = array();

// sql update
$temp_title = htmlspecialchars($title,ENT_QUOTES);
$section_array[section_title] = nl2br($temp_title);

$section_array[user_id] = $moderator;
$section_array[parent_id] = $section_id;
$section_array[section_weight] = $weight;

$temp_intro = htmlspecialchars($description,ENT_QUOTES);
$section_array[section_intro] = nl2br($temp_intro);


//call add section function from database_layer
if(!add_section($section_array)){
	nexus_error();
}

//redirect to the parent section  
#echo "Redirecting to ".$_SERVER['HTTP_HOST']."/section.php?section_id=$parent_section_array[section_id]";
header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=$parent_section_array[section_id]");
exit();
}


?>