<?php
# leaps to the next section containg unread messages or to main menu

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
# include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


// parameters
$section_id = $_GET[section_id];
$topic_id = $_GET[topic_id];

$db = opendata();
session_start();

// check login
if (!validlogin()){
	eject_user();	
}

unsubscribe_from_topic($topic_id, $_SESSION[current_id]);

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=$section_id");
?>






