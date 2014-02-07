<?php
// updates userinfo

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');


// parameters
$user_realname = $_POST['user_realname'];
$user_email = $_POST['user_email'];
$user_popname = $_POST['user_popname'];
$user_sex = $_POST['user_sex'];
$user_display = $_POST['show_post'];
$user_town = $_POST['user_town'];
$user_film = $_POST['user_film'];
$user_band = $_POST['user_band'];
$user_age = $_POST['user_age'];
$user_comment = $_POST['user_comment'];

if (isset($_POST['pictures'])) {
    $pictures = true;
} else {
    $pictures = false;
}


if (isset($_POST['backwards'])) {
    $backwards = true;
} else {
    $backwards = false;
}

if (isset($_POST['hideemail'])) {
    $hideemail = true;
} else {
    $hideemail = false;
}

// function 

//common stuff

$db = opendata();
session_start();

// check login
if (!validlogin()) {
    eject_user();
}

// check if user exists if not check them to the main menu
$user_array = get_user_array($_SESSION['current_id']);


if ($pictures) {
    $user_array['user_no_pictures'] = 'y';
    $_SESSION['no_pictures'] = 'y';
} else {
    $user_array['user_no_pictures'] = 'n';
    # update session var
    $_SESSION['no_pictures'] = 'n';
}


if ($backwards) {
    $user_array['user_backwards'] = 'y';
} else {
    $user_array['user_backwards'] = 'n';
}

if ($hideemail) {
    $user_array['user_hideemail'] = 'yes';
} else {
    $user_array['user_hideemail'] = 'no';
}

$user_array['user_realname'] = $user_realname;
$user_array['user_email']    = $user_email;
$user_array['user_popname']  = $user_popname;
$user_array['user_sex']      = $user_sex;
$user_array['user_display']  = $user_display;
$user_array['user_town']     = $user_town;
$user_array['user_film']     = $user_film;
$user_array['user_town']     = $user_town;
$user_array['user_band']     = $user_band;
$user_array['user_age']      = $user_age;
$user_array['user_comment']  = $user_comment;

// escape input messes with line breaks so this is bad here
// $user_array['user_comment'] = escape_input($user_comment);

//$user_array['user_comment'] = htmlspecialchars($user_comment, ENT_NOQUOTES);

if (!update_user_array($user_array)) {
    show_error($_SERVER['PHP_SELF']."update_user_array failed<br> user_array is ".print_r($user_array, true));
}

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");
exit();
