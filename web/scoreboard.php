<?php
/* 

displays users who are currently using nexus

*/

//includes

include('../includes/database.php');


//common stuff
$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$my_theme; 

// check login
if (!validlogin()){
	eject_user();	
}

$user_array = get_user_array($_SESSION[current_id]);

$users_on_array = get_users_online($_SESSION[current_id], true);

$breadcrumbs = '<font size="-1"><a href="section.php?section_id=1">Main Menu</a> -&gt; </font>';

$t = new Template($template_location);

 display_header($t,
	       $breadcrumbs,
	       "Nexus Scoreboard", # should the BBS name be defined?
	       $user_array["user_name"],
	       $user_array["user_popname"],
	       $_SESSION[current_id],
	       count_instant_messages($_SESSION[current_id]),
	       SYSOP_ID,
	       SYSOP_NAME,
	       get_count_unread_comments($_SESSION[current_id]),
	       get_count_unread_messages($_SESSION[current_id]));


//update user activity
update_location("Nexus Scoreboard");

$t->set_file("scoreboard", "scoreboard.html");

$t->set_block('scoreboard', 'PosterBlock', 'posterrow'); 
if ($poster_array = get_top_posters_array()) {

	foreach ($poster_array  as $current_poster_array){
	  $t->set_var("user_id", $current_poster_array[user_id]);
	  $t->set_var("user_name", $current_poster_array[user_name]);
	  $t->set_var("user_score", $current_poster_array[total]);
	  $t->parse('posterrow', 'PosterBlock', true);
	}    
}


$t->set_block('scoreboard', 'ModBlock', 'modrow'); 
if ($mod_array = get_top_moderator_array()) {

	foreach ($mod_array  as $current_mod_array){
	  $t->set_var("mod_id", $current_mod_array[user_id]);
	  $t->set_var("mod_name", $current_mod_array[user_name]);
	  $t->set_var("mod_score", $current_mod_array[total]);
	  $t->parse('modrow', 'ModBlock', true);
	}    
}


$t->set_block('scoreboard', 'SectionBlock', 'sectionrow'); 
if ($sec_array = get_top_section_array()) {

	foreach ($sec_array  as $current_sec_array){
	  $t->set_var("section_id", $current_sec_array[section_id]);
	  $t->set_var("section_name", $current_sec_array[section_title]);
	  $t->set_var("section_score", $current_sec_array[total]);
	  $t->parse('sectionrow', 'SectionBlock', true);
	}    
}



$t->set_block('scoreboard', 'TopicBlock', 'topicrow'); 
if ($topic_array = get_top_topic_array()) {

	foreach ($topic_array  as $current_topic_array){
	  $t->set_var("topic_id", $current_topic_array[topic_id]);
	  $t->set_var("topic_name", htmlspecialchars($current_topic_array[topic_title],ENT_QUOTES));
	  $t->set_var("topic_score", $current_topic_array[total]);
	  $t->parse('topicrow', 'TopicBlock', true);
	}    
}


$t->pparse("MyFinalOutput","scoreboard");

page_end($breadcrumbs,$t);
?>






