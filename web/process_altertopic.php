<?php
// new add post code - interface

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');


// parameters

$title = $_POST['title'];
$section_id = $_POST['section_id'];
$topic_id = $_POST['topic_id'];
$description = $_POST['description'];
$weight = $_POST['weight'];
$section = $_POST['section'];

if (isset($_POST['readonly'])) {
    $readonly = $_POST['readonly'];
} else {
    $readonly = 'no';
}


if (isset($_POST['secret'])) {
    $secret = $_POST['secret'];
} else {
    $secret = 'no';
}

if (isset($_POST['hidden'])) {
    $hidden = $_POST['hidden'];
} else {
    $hidden = 'no';
}

$db = opendata();
session_start();
# $template_location =TEMPLATE_HOME.$_SESSION[my_theme]; 
#$t = new Template($template_location);


// check login
if (!validlogin()) {
    eject_user();
}

if (!$user_array = get_user_array($_SESSION['current_id'])) {
    nexus_error();
}

// can_user_edit fuction here

if (!$topic_array = get_topic($topic_id)) {
    // no such topic
    header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");
    exit();
} else {
    // topic exists

    //    echo "gets here";
    if (!can_user_edit_topic($user_array, $topic_array)) {
        // topic exists but user can not edit it
        header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().'section.php?section_id='.$topic_array['section_id']);
        exit();
    }

    // at this point the current user can edit the section

    //check http vars
    //strip html from topic title

    $topic_array['topic_title'] = htmlspecialchars($title);
    $topic_array['section_id'] = $section;

  //  $topic_array['topic_description'] = nl2br($description);
    $topic_array['topic_description'] = $description;

    if ($secret === 'yes') {
        $topic_array['topic_annon'] = 'y';
    } else {
        $topic_array['topic_annon'] = 'n';
    }

    if ($readonly === 'yes') {
        $topic_array['topic_readonly'] = 'y';
    } else {
        $topic_array['topic_readonly'] = 'n';
    }

    if ($hidden === 'yes') {
        $topic_array['topic_title_hidden'] = 'y';
    } else {
        $topic_array['topic_title_hidden'] = 'n';
    }

    $topic_array['topic_weight'] = $weight;


    // update
    if (update_topic($topic_array)) {
    //worked
    } else {
        echo "here";
        nexus_error();
    }

    header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().'section.php?section_id='.$topic_array['section_id']);

}
