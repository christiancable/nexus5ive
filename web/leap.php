<?php
/*
leap to a section with an unread topic OR leap to the main menu
*/

//includes
include('../includes/database.php');


//common stuff
$db = opendata();
session_start();

// check login
if (!validlogin()){
	eject_user();
}


if($target = find_next_unread_topic($_SESSION[current_id])){

} else {
	$target = 1;
}

header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section=$target");
?>
