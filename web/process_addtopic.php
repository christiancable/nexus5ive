<?php
// new add post code - interface


include('../includes/database.php');

// parameters
$section_id = $_POST[section_id];

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

if (!$section_array = get_section($section_id))
    nexus_error();

// can_user_edit fuction here

if(!can_user_edit_section($user_array, $section_array)){
	# user can not add a topic in this section, bounce them to the main menu
	header("Location: http://".$_SERVER[HTTP_HOST].get_bbsroot()."section.php?section_id=".$topic_array[section_id]);
	exit();
}

   
#strip html from topic title
$topic_array[topic_title] = htmlspecialchars($HTTP_POST_VARS[title]);
$topic_array[section_id] = $HTTP_POST_VARS[section];
	

$topic_array[topic_description] = nl2br($HTTP_POST_VARS[description]);
	
if($HTTP_POST_VARS[secret]=='yes'){
	$topic_array[topic_annon] = 'y';
} else {
	$topic_array[topic_annon] = 'n';
}
	
if($HTTP_POST_VARS[readonly]=='yes'){ 
	$topic_array[topic_readonly] = 'y';
} else {
	$topic_array[topic_readonly] = 'n';
}
	
$topic_array[topic_weight] = $HTTP_POST_VARS[weight];
	
// update 
if(add_topic($topic_array)){
	//worked
} else {
	nexus_error();
}



header("Location: http://".$_SERVER[HTTP_HOST].get_bbsroot()."section.php?section_id=".$topic_array[section_id]); 

?>
