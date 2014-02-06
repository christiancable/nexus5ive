<?php

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


// parameters

$section_id=$_GET['section_id'];

$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION['my_theme'];
$t = new Template($template_location);


// check login
if (!validlogin()) {
    eject_user();
}

if (!$user_array = get_user_array($_SESSION['current_id'])) {
    show_error($_SERVER['PHP_SELF']."get_user_array failed<br>SESSION is ".print_r($_SESSION, true));
}

// can_user_edit fuction here

if (!$section_array = get_section($section_id)) {
    // no such section
    header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");
    exit();
} else {

  // section exists
    if (!can_user_edit_section($user_array, $section_array)) {
        header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=".$section_array['section_id']);
        exit();
    }
  
    // at this point the current user can edit the section

    if ($subsection_array_list=get_subsectionlist_array($section_array['section_id'])) {
        header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=".$section_array['parent_id']);
        exit();
    }

    //section owner info
    // this is just a simple username look up
    if (!$moderator_name = get_username($section_array['user_id'])) {
        show_error($_SERVER['PHP_SELF']."get_user_array failed<br>SESSION is ".print_r($_SESSION, true));
    }

    if (!$breadcrumbs = get_breadcrumbs($section_array['section_id'])) {
        show_error($_SERVER['PHP_SELF']."get_breadcrumbs failed <br>section_array is ".print_r($section_array, true));
    }
    // show header
    // if we have sub sections bounce them back

    display_header(
        $t,
        $breadcrumbs,
        "Delete ".$section_array['section_title']."?",
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

    // show modify section comment
  
    $t->set_file("sectionform", "delsection.html");
  
  
    // SELECT
  
    // get number of topics in section to check there is no subsections
    $topics_list = get_section_topics($section_array['section_id']);
    if (!$subsection_array_list) {
        if ($topics_list) {
            $t->set_var("TOPICS_COUNT", count($topics_list));
        } else {
            $t->set_var("TOPICS_COUNT", " no ");
        }
        $t->set_var("PARENT_ID", $section_array['parent_id']);
        $t->set_var("SECTION_ID", $section_array['section_id']);
    }
    // get array of topics in section and delete each one

    $t->pparse("SectionOutput", "sectionform");

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
