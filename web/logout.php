<?php

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');

$db = opendata();
session_start();

setuser_offline($_SESSION['current_id']);

session_destroy();

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot());
exit;
?>






