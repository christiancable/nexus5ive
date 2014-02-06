<?php
/**********************************************/
/* delete_instant_messages.php                */
/*                                            */
/* remove a users instant messaeges           */
/* called from messages.php                   */
/**********************************************/

// includes
include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');

// parameters

$message_id_array = $HTTP_POST_VARS['MessChk'];

$db = opendata();
session_start();

// check login
if (!validlogin()) {
    eject_user();
}


delete_instant_messages($message_id_array);

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."messages.php");
