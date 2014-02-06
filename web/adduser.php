<?php
/**********************************************/
/* adduser.php                                */
/*                                            */
/* allows guests to sign up for user accounts */
/**********************************************/

// includes

include_once('../includes/common.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

if (isset($_GET['error'])) {
    $error = $_GET['error'];
} else {
    $error = false;
}

//common stuff
// $db = opendata();
$template_location = TEMPLATE_HOME.DEFAULT_TEMPLATE;

switch ($error) {
     
    case "age":
        $info = "<b>Too Young!</b><br>
        I'm sorry ".BBS_NAME." can be a strange place with many a rude work<br>
        You can not join in unless you tick the box below to confirm that you are at least ".BBS_AGE;
        break;

    case "missing":
        $info = "<b>Missing Info!</b><br>
        Please make sure all the information is filled out below otherwise we can not<br>
        let you come and play";
        break;

    case "password":
        $info = "<b>Eeek! The passwords don't match!</b><br>
        Please refill the information below making sure that both passwords match.";
        break;

    case "existing_user":
        $info = "<b>Whoops! That username is already taken!</b><br>
        Please choose another username.";
        break;

    case "existing_email":
        $info = '<b>Hold on a minute ... That email address already has an account!</b><br>
        If this is a mistake or you want to change your username please contact <a href="mailto:'.
        SYSOP_MAIL.'">'.SYSOP_NAME.'</a>';
        break;

    default:
        $info = "Fill in the details below to create your very own user account<br><br>
        Once your account is activated you'll be able to take part in discussions,<br>
        send instant messages and generally have a fab'ol time. ";

}


$breadcrumbs = "";
$t = new Template($template_location);

// should make display_header - guest aware
display_header(
    $t,
    $breadcrumbs,
    "Join ".BBS_NAME,
    "Guest",
    "just looking",
    0,
    0,
    SYSOP_ID,
    SYSOP_NAME,
    0,
    0
);

$t->set_file("createuser", "adduser.html");
$t->set_var("info_text", $info);
$t->set_var("BBS_NAME", BBS_NAME);
$t->set_var("BBS_AGE", BBS_AGE);
$t->pparse("MyFinalOutput", "createuser");

page_end($breadcrumbs, $t);
