<?php
/**********************************************/
/* catchup.php                                */
/*                                            */
/* makes all topics as read                   */
/**********************************************/

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

catchup($_SESSION['current_id']);


header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");
