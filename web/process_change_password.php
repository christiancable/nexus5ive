<?php
/* 

backend for the change password screen

*/

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');


//common stuff
$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION['my_theme'];

// check login
if (!validlogin()) {
    eject_user();
}

$user_array = get_user_array($_SESSION['current_id']);

$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$new_password2 = $_POST['new_password2'];


if (($current_password === $user_array['user_password']) and ($new_password === $new_password2)) {

    change_password($_SESSION['current_id'], $new_password);
    header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."show_userinfo.php?user_id=$_SESSION[current_id]");
} else {
    header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."change_password.php?error=1");
}
