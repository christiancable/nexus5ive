<?php

include('../includes/database.php');

$db = opendata();
session_start();

// check login
if (!validlogin()){
	eject_user();	
}

# delete messages

#delete_instant_messages($_SESSION[current_id]);

$instant_message = array();
#get post vars
$instant_message[text]= htmlspecialchars($HTTP_POST_VARS[message], ENT_QUOTES);
$instant_message[from_id] = $_SESSION[current_id];
$instant_message[user_id] = $HTTP_POST_VARS[user_id]; # should I check this guy exists or do I trust the form

add_nexusmessage($instant_message);

header("Location: http://".$_SERVER['HTTP_HOST']."/messages.php");


?>