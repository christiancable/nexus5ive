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

delete_instant_messages($_SESSION[current_id]);

header("Location: http://".$_SERVER['HTTP_HOST']."/messages.php");


?>