<?php
// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');

$db = opendata();
session_start();

// check login
if (!validlogin())
{
  eject_user();	
}

# delete messages

delete_user_comments($_SESSION['current_id']);

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().
       "show_userinfo.php?user_id=".$_SESSION['current_id']);

?>