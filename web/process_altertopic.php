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
// topic exists 

//	echo "gets here";
	if(!can_user_edit_topic($user_array, $topic_array)){
		// topic exists but user can not edit it
    	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=$topic_array[section_id]");
	    exit();
    }
     
	// at this point the current user can edit the section 
  
	//get and check http vars
	#strip html from topic title
	$topic_array[topic_title] = htmlspecialchars($HTTP_POST_VARS[title]);
	$topic_array[section_id] = $HTTP_POST_VARS[section];
	
	//echo  "<h1>".$HTTP_POST_VARS[section]."</h1>";
	//exit();

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

	if($HTTP_POST_VARS[hidden]=='yes'){
		$topic_array[topic_title_hidden] = 'y';
	} else {
		$topic_array[topic_title_hidden] = 'n';
	}

	$topic_array[topic_weight] = $HTTP_POST_VARS[weight];
	

	// update 
	if(update_topic($topic_array)){
		//worked
	} else {
		echo "here";
		nexus_error();
	}

	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=$topic_array[section_id]"); 	
# UPDATE include breadcrumbs and bottom code
}


?>
