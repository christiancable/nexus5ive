<?php
/**********************************************/
/* welcome.php                                */
/*                                            */
/* welcome page for new users                 */
/**********************************************/

// includes
include_once('../includes/common.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

if (isset($_GET['status'])) {
    $status = $_GET['status'];
} else {
    // guessing here
    $status = "validate";
}


$template_location = TEMPLATE_HOME.DEFAULT_TEMPLATE;

$breadcrumbs = "";
$t = new Template($template_location);

// should make display_header - guest aware
display_header(
    $t,
    $breadcrumbs,
    "Welcome to  ".BBS_NAME,
    "Guest",
    "just looking",
    0,
    0,
    SYSOP_ID,
    SYSOP_NAME,
    0,
    0
);

if ($status === "ready") {
    $t->set_file("welcomeuser", "account_ready.html");
} else {
    $t->set_file("welcomeuser", "account_not_ready.html");
}

$t->set_var("SYSOP_MAIL", SYSOP_MAIL);
$t->set_var("BBS_NAME", BBS_NAME);
$t->set_var("SYSOP_NAME", SYSOP_NAME);

$t->pparse("MyFinalOutput", "welcomeuser");

page_end($breadcrumbs, $t);
