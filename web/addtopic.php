<?php
// new add post code - interface

include('../includes/database.php');

// parameters
$section_id = $_GET[section_id];

$db = opendata();
session_start();
$template_location = TEMPLATE_HOME . $_SESSION[my_theme];
$t = new Template($template_location);

if (!validlogin()) {
    eject_user();
} 

if (!$user_array = get_user_array($_SESSION[current_id])) {
    nexus_error();
} 

if (!$section_array = get_section($section_id)){
    nexus_error();
}

if (!$moderator_name = get_username($section_array[user_id])) {
    nexus_error();
} 

# can user add a topic to this section
if (!can_user_edit_section($user_array, $section_array)){
    # section exists but the user can not add topics here
    header("Location: http://" . $_SERVER['HTTP_HOST'] .get_bbsroot(). "section.php?section_id=1");
    exit();
}

if (!$breadcrumbs = get_breadcrumbs_topic($section_array[section_id])){
    nexus_error(); 
}

/* new header code */
display_header($t,
	       $breadcrumbs,
	       $section_array[section_title],
	       $user_array["user_name"],
	       $user_array["user_popname"],
	       $_SESSION[current_id],
	       count_instant_messages($_SESSION[current_id]),
	       $section_array[user_id],
	       $moderator_name,
	       get_count_unread_comments($_SESSION[current_id]),
	       get_count_unread_messages($_SESSION[current_id]));



#####

$t -> set_file("topicform", "addtopic.html");
$t -> set_var("SECTION_ID", $section_array[section_id]);
// SELECT
$select_code = '<option value="' . $section_array[section_id] . '">' . $section_array[section_title] . '</option>';

$sectionlist_array = get_sectionlist_array($user_array);

foreach ($sectionlist_array as $current_element) {
    $select_code = $select_code . '<option value="' . $current_element[section_id] . '">' . $current_element[section_title] . '</option>';
} 
$t -> set_var("SELECT_CODE", $select_code);

$t -> pparse("TopicOutput", "topicform");

page_end($breadcrumbs, $t);
// UPDATE include breadcrumbs and bottom code
 

?>
