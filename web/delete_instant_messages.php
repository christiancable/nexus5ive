<?php

// parameters

$message_id_array = $HTTP_POST_VARS[MessChk];

include('../includes/database.php');

$db = opendata();
session_start();

// check login
if (!validlogin()){
	eject_user();	
}


delete_instant_messages($message_id_array);

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."messages.php");


?>
