<?php 
// new add post code - interface
include('../includes/theme.php');
include('../includes/database.php');

$db = opendata();
session_start();
$template_location = TEMPLATE_HOME . $my_theme;
$t = new Template($template_location);

if (!validlogin()) {
    eject_user();
} 

if (!$user_array = get_user_array($_SESSION[current_id])) {
    nexus_error();
} 

$section_array = get_section($topic_array[section_id]);

if (!$moderator_name = get_username($section_array[user_id])) {
    // if not section owner then bounce them back to the main menu
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/section.php?section_id=1");
    exit();
} 

if (!$breadcrumbs = get_breadcrumbs_topic($section_array[section_id]))
    nexus_error(); 
// show header
// change page template if new messages are waiting
if (get_count_unread_messages($_SESSION[current_id]) > 0) {
    $t -> set_file("header", "mail_page.html");
} else {
    $t -> set_file("header", "page.html");
} 

if ($num_msg = count_instant_messages($_SESSION[current_id])) {
    $t -> set_var("num_msg", $num_msg);
} else {
    $t -> set_var("num_msg", "no");
} 

$t -> set_var("pagetitle", "Updating " . $topic_array[topic_title]);
$t -> set_var("breadcrumbs", $breadcrumbs);

$t -> set_var("owner_id", $section_array[user_id]);
$t -> set_var("ownername", $moderator_name);

$t -> set_var("user_name", $user_array["user_name"]);
$t -> set_var("user_popname", $user_array["user_popname"]);
$t -> set_var("user_id", $_SESSION[current_id]);

$t -> set_var("section_id", $section_array[section_id]);
$t -> pparse("HeaderOutput", "header");

$t -> set_file("topicform", "addtopic.html");
$t -> set_var("SECTION_ID", $topic_array[section_id]);
// SELECT
$select_code = '<option value="' . $section_array[section_id] . '">' . $section_array[section_title] . '</option>';

$sectionlist_array = get_sectionlist_array($user_array);

foreach ($sectionlist_array as $current_element) {
    $select_code = $select_code . '<option value="' . $current_element[section_id] . '">' . $current_element[section_title] . '</option>';
} 
$t -> set_var("SELECT_CODE", $select_code);

$t -> pparse("TopicOutput", "topicform");

page_end($breadcrumbs);
// UPDATE include breadcrumbs and bottom code
 

?>