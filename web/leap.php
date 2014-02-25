<?php
/*
leap to a section with an unread topic OR leap to the main menu
*/

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');

//common stuff
$db = opendata();
session_start();

// check login
if (!validlogin()) {
    eject_user();
}

$target = find_next_unread_topic($_SESSION['current_id']);
if ($target === false) {
    $target = 1;
}

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=$target");
