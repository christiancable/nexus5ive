<?php

include('../includes/database.php');

$db = opendata();
session_start();

// check login
if (!validlogin()){
	eject_user();	
}

# delete messages

$message_id_array = $HTTP_POST_VARS[MessChk];

delete_instant_messages($message_id_array);
header("Location: http://".$_SERVER['HTTP_HOST']."/messages.php");


?>
