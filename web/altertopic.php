<?php
// alter topic interface

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

// parameters

$topic_id = $_GET['topic_id'];

$db = opendata();
session_start();
$template_location = TEMPLATE_HOME.$_SESSION['my_theme'];
$t = new Template($template_location);


// check login
if (!validlogin()) {
    eject_user();
}

if (!$user_array = get_user_array($_SESSION['current_id'])) {
    show_error($_SERVER['PHP_SELF'].'get_user_array failed<br>SESSION is '.print_r($_SESSION, true));
}

// can_user_edit fuction here

if (!$topic_array = get_topic($topic_id)) {
    // no such topic
    header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");
    exit();
} else {

    // topic exists
    if (!can_user_edit_topic($user_array, $topic_array)) {
        // topic exists but user can not edit it
        header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=$topic_array[section_id]");
        exit();
    }
  
    // at this point the current user can edit the section

    //section owner info
    // this is just a simple username look up
    $section_array = get_section($topic_array['section_id']);

    if (!$moderator_name = get_username($section_array['user_id'])) {
        show_error($_SERVER['PHP_SELF']."get_username failed for moderator<br>section_array is ".print_r($section_array, true));
    }
    if (!$breadcrumbs = get_breadcrumbs_topic($section_array['section_id'])) {
        show_error($_SERVER['PHP_SELF']."get_breadcrumbs_topic failed <br>section_array is ".print_r($section_array, true));
    }

    // show header
    /* new header code */
    display_header(
        $t,
        $breadcrumbs,
        "Updating ".$topic_array['topic_title'],
        $user_array['user_name'],
        $user_array['user_popname'],
        $_SESSION['current_id'],
        count_instant_messages($_SESSION['current_id']),
        $section_array['user_id'],
        $moderator_name,
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
  
  
    #####
  
    $t->set_file("topicform", "altertopic.html");

    $t->set_var("TOPIC_NAME", $topic_array['topic_title']);
    $t->set_var("SECTION_ID", $topic_array['section_id']);

    //  $t->set_var("DESCRIPTION", ereg_replace("<br />","",$topic_array['topic_description']));
    $t->set_var("DESCRIPTION", $topic_array['topic_description']);
    $t->set_var("WEIGHT", $topic_array['topic_weight']);
    $t->set_var("TOPIC_ID", $topic_array['topic_id']);
    // annon
    if ($topic_array['topic_annon'] == 'y') {
        $t->set_var("SECRET_CHECKED", "checked");
    } else {
        $t->set_var("SECRET_CHECKED", "");
    }
  
  // readonly
  
    if ($topic_array['topic_readonly'] == 'y') {
        $t->set_var("READONLY_CHECKED", "checked");
    } else {
        $t->set_var("READONLY_CHECKED", "");
    }

    if ($topic_array['topic_title_hidden'] == 'y') {
        $t->set_var("HIDDEN_CHECKED", "checked");
    } else {
        $t->set_var("HIDDEN_CHECKED", "");
    }

  // SELECT
  
    $select_code = '<option value="'.$section_array['section_id'].'">'.$section_array['section_title'].'</option>';

    $sectionlist_array=get_sectionlist_array($user_array);

    foreach ($sectionlist_array as $current_element) {
        $select_code = $select_code.'<option value="'.$current_element['section_id'].'">'.$current_element['section_title'].'</option>';
    }

    $t->set_var("SELECT_CODE", $select_code);
    $t->pparse("TopicOutput", "topicform");

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
}
