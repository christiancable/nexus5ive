<?php

// parameters
$message = $_POST[message];
$user_id = $_POST[user_id];

include('../includes/database.php');

$db = opendata();
session_start();


// check login
if (!validlogin()){
	eject_user();	
}


$instant_message = array();
#get post vars
$instant_message[text]= htmlspecialchars($message, ENT_QUOTES);
$instant_message[from_id] = $_SESSION[current_id];
$instant_message[user_id] = $user_id; # should I check this guy exists or do I trust the form

add_nexusmessage($instant_message);

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot(). "messages.php");


?>