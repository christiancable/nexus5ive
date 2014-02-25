<?php

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
# include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

define('MAX_INST_MESSAGE_LENGTH', 199);

// parameters
$message = $_POST['message'];
$user_id = $_POST['user_id'];

$db = opendata();
session_start();


// check login
if (!validlogin()) {
    eject_user();
}


$instant_message = array();

# truncate the text at max message length
$instant_message['text'] = substr(htmlspecialchars($message, ENT_QUOTES), 0, MAX_INST_MESSAGE_LENGTH);
$instant_message['from_id'] = $_SESSION['current_id'];
$instant_message['user_id'] = $user_id; # should I check this guy exists or do I trust the form

if ($instant_message['user_id'] == 'ALL') {
    $users_on_array = array();
    if ($users_on_array = get_users_online($_SESSION['current_id'], false)) {
        foreach ($users_on_array as $currentUserArray) {
            $instant_message['user_id'] = $currentUserArray['user_id'];
            # make messages to all more shouty
            $instant_message['text'] = '<b>'.$instant_message['text'].'</b>';
            add_nexusmessage($instant_message);
        }
    } else {

    }
} else {
    add_nexusmessage($instant_message);
}

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot(). "messages.php");
