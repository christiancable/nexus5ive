<?php
include('../includes/theme.php');
include('../includes/database.php');

$db = opendata();
session_start();

// check login
if (!validlogin()){
	eject_user();	
}

# delete messages

delete_user_comments($_SESSION[current_id]);

#header("Location: http://".$_SERVER['HTTP_HOST']."/messages.php");
header("Location: http://".$_SERVER['HTTP_HOST']."/show_userinfo.php?user_id=".$_SESSION[current_id]);

?>