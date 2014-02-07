<?php
// displays search form and 
// paginates any existing results

// cfc - may 2004

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


//common stuff
$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION['my_theme'];

// check login
if (!validlogin()) {
    eject_user();
}

//update user activity

$user_array = get_user_array($_SESSION['current_id']);

update_location('<a href="search.php">Search</a>');

$users_on_array = get_users_online($_SESSION['current_id'], true);

$breadcrumbs = get_dummybreadcrumbs();

$t = new Template($template_location);

display_header(
    $t,
    $breadcrumbs,
    "Search",
    $user_array['user_name'],
    $user_array['user_popname'],
    $_SESSION['current_id'],
    count_instant_messages($_SESSION['current_id']),
    SYSOP_ID,
    SYSOP_NAME,
    get_count_unread_comments($_SESSION['current_id']),
    get_count_unread_messages($_SESSION['current_id'])
);

display_navigationBar(
    $topicleap = true,
    $whosonline = true,
    $mainmenu = false,
    $examineuser = true,
    $returntosection = false,
    $createtopic = false,
    $createmenu = false,
    $postcomment = false,
    $section_id = false,
    $parent_id = false,
    $topic_id = false
);

$num_of_users = count($users_on_array);

  
$t->set_file("search", "search.html");
$t->pparse("MyFinalOutput", "search");

display_navigationBar(
    $topicleap = true,
    $whosonline = true,
    $mainmenu = false,
    $examineuser = true,
    $returntosection = false,
    $createtopic = false,
    $createmenu = false,
    $postcomment = false,
    $section_id = false,
    $parent_id = false,
    $topic_id = false
);

page_end($breadcrumbs, $t);
