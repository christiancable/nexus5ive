<?php


// parameters
$section_id=$_POST[section_id];
$confirm=$_POST[confirm];


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
	header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");
	exit();
} else {
	// section exists
	if(!can_user_edit_section($user_array, $section_array)){
		header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=$section_array[section_id]");
		exit();
	}

}
// at this point the current user can edit the section

if($subsection_array_list=get_subsectionlist_array($section_array[section_id])) {
	header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=$section_array[parent_id]");
	exit();
}

if($confirm!="yes"){
	header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=$section_array[parent_id]");
	exit();
}



# get number of topics in section to check there is no subsections
$topics_list = get_section_topics($section_array[section_id]);

if($topics_list){
	foreach($topics_list as $topic_array){
		#echo "<!-- removing ".$topic_array[topic_title]." --!><br/>";
		if(!delete_topic($topic_array[topic_id])){
			nexus_error();
		}
	}
}


if(!delete_section($section_array[section_id])){
	nexus_error();
}

# redirect to parent_id section
header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=$section_array[parent_id]");
exit();
?>
