<?php
// new add post code - interface

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


// parameters

//get form data
$title = $_POST[title];
$description = $_POST[description];
$weight = $_POST[weight];
$moderator = $_POST[moderator];
$section_id = $_POST[section_id];

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
	header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");
    exit();  	
	
	
} else {
// section exists 
	if(!can_user_edit_section($user_array, $parent_section_array)){
    	header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=$parent_section_array[section_id]");
	    exit();
    }
     
// at this point the current user can edit the parent section
  


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
header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=$parent_section_array[section_id]");
exit();
}


?>