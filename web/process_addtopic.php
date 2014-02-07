<?php
// backend to add topic

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');


// parameters
$section_id = $_POST['section_id'];
$topic_title = $_POST['title'];

$dest_section_id = $_POST['section'];

$description = $_POST['description'];
$weight = $_POST['weight'];

if (isset($_POST['readonly'])) {
    $readonly = 'y';
} else {
    $readonly = 'n';
}

if (isset($_POST['secret'])) {
    $secret = 'y';
} else {
    $secret = 'n';
}



$db = opendata();
session_start();

// check login
if (!validlogin()) {
    eject_user();
}

if (!$user_array = get_user_array($_SESSION['current_id'])) {
    show_error($_SERVER['PHP_SELF']."get_user_array failed<br/> SESSION is ".var_dump($_SESSION));
}

if (!$section_array = get_section($section_id)) {
    show_error($_SERVER['PHP_SELF']."get_section failed<br/> section_id is ".$section_id);
}
// can_user_edit fuction here

if (!can_user_edit_section($user_array, $section_array)) {
    // user can not add a topic in this section, bounce them to the main menu
    header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=".$topic_array['section_id']);
    exit();
}

// strip html from topic title
$topic_array['topic_title'] = htmlspecialchars($topic_title);

$topic_array['section_id'] = $dest_section_id;
//$topic_array['topic_description'] = nl2br($description);
$topic_array['topic_description'] = $description;

if ($secret=='yes') {
    $topic_array['topic_annon'] = 'y';
} else {
    $topic_array['topic_annon'] = 'n';
}
    
if ($readonly=='yes') {
    $topic_array['topic_readonly'] = 'y';
} else {
    $topic_array['topic_readonly'] = 'n';
}
    
$topic_array['topic_weight'] = $weight;
    
// update 
if (add_topic($topic_array)) {
    //worked
} else {
    show_error($_SERVER['PHP_SELF']."add_topic failed<br/>".var_dump($topic_array));
}

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=".$topic_array['section_id']);
