<?php

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


$db = opendata();
session_start();

# get vars 
$section = $_GET['section_id'];

$template_location = TEMPLATE_HOME . $_SESSION['my_theme'];
// check login

if (!validlogin()) {
    eject_user();
}
// ADD CHECKS HERE
$user_array = get_user_array($_SESSION['current_id']);



if (!$sectioninfodetails = get_section($section)) {
    nexus_error();
}

$sectionname = $sectioninfodetails['section_title'];
$sectionowner = $sectioninfodetails['user_id'];
$sectionparent = $sectioninfodetails['parent_id'];

$breadcrumbs = get_breadcrumbs($section);

$location_str = '<a href="section.php?section_id=' . $sectioninfodetails['section_id'] . '">' . $sectionname . '</a>';

update_location($location_str);

if (!$ownername = get_username($sectionowner)) {
    $ownername = "unknowm moderator";
}

$t = new Template($template_location);


display_header(
    $t,
    $breadcrumbs,
    $sectionname,
    $user_array['user_name'],
    $user_array['user_popname'],
    $_SESSION['current_id'],
    count_instant_messages($_SESSION['current_id']),
    $sectionowner,
    $ownername,
    get_count_unread_comments($_SESSION['current_id']),
    get_count_unread_messages($_SESSION['current_id'])
);

if (is_section_owner($sectioninfodetails['section_id'], $user_array['user_id'], $db)) {

    display_navigationBar(
        $topicleap = true,
        $whosonline = true,
        $mainmenu = false,
        $examineuser = true,
        $returntosection = false,
        $createtopic = true,
        $createmenu = false,
        $postcomment = false,
        $section_id = $sectioninfodetails['section_id'],
        $parent_id = false,
        $topic_id = false
    );

} else {

    display_navigationBar(
        $topicleap = true,
        $whosonline = true,
        $mainmenu = false,
        $examineuser = true,
        $returntosection = false,
        $createtopic = false,
        $createmenu = false,
        $postcomment = false,
        $section_id = $sectioninfodetails['section_id'],
        $parent_id = false,
        $topic_id = false
    );

}


// END DISPLAY TOP SET OF BUTTONS

// ## topics

// gets here fine - 20091008

if ($topics_list = get_section_topics($section)) {

    foreach ($topics_list as $current_topic_array) {

        if (new_messages_in_topic($current_topic_array['topic_id'], $_SESSION['current_id'])) {

            if (can_user_edit_topic($user_array, $current_topic_array)) {

                if (!unsubscribed_from_topic($current_topic_array['topic_id'], $_SESSION['current_id'])) {
                    $mode = "NEW_ADMIN_SUB";
                } else {
                    $mode = "NEW_ADMIN_UNSUB";
                }
            } else {

                if (!unsubscribed_from_topic($current_topic_array['topic_id'], $_SESSION['current_id'])) {
                    $mode = "NEW_NORMAL_SUB";
                } else {
                    $mode = "NEW_NORMAL_UNSUB";
                }
            }

        } else {

            if (can_user_edit_topic($user_array, $current_topic_array)) {

                if (!unsubscribed_from_topic($current_topic_array['topic_id'], $_SESSION['current_id'])) {
                    $mode = "ADMIN_SUB";
                } else {
                    $mode = "ADMIN_UNSUB";
                }

            } else {

                if (!unsubscribed_from_topic($current_topic_array['topic_id'], $_SESSION['current_id'])) {
                    $mode = "NORMAL_SUB";
                } else {
                    $mode = "NORMAL_UNSUB";
                }
            }
        }

        display_topic($current_topic_array, $_SESSION['current_id'], $t, $mode);

    }

}


// ########## subsections

$subsectionlist = get_subsectionlist_array($section);

$numberOfSubSections = count($subsectionlist);
$numberOfLeftSubSections = round($numberOfSubSections/2);

$currentSubSection = 0;
if ($subsectionlist) {
    echo '<div id="SubSectionContent">';
    echo "<!-- left column -->";
    echo '<div class="LeftSubSection">';

    foreach ($subsectionlist as $current_sub_section_array) {
        $currentSubSection ++;

        // this just seems to display the last topic rather than the section!? WTF! June 2010
        display_sectionheader($current_sub_section_array, $user_array, $t);

        if ($currentSubSection == $numberOfLeftSubSections) {
            echo '</div>';
            echo '<!-- right column -->';
            echo '<div class="RightSubSection">';
        }

    }

    // clear floats - my goodness this was hard to find
    echo '</div>';
    echo '<div style="clear:both;"></div>';
    echo '</div>';

} else {
  // no subsections
}

// BEGIN DISPLAY BOTTOM SET OF BUTTONS

if (is_section_owner($sectioninfodetails['section_id'], $user_array['user_id'], $db)) {
    display_navigationBar(
        $topicleap = true,
        $whosonline = true,
        $mainmenu = false,
        $examineuser = true,
        $returntosection = false,
        $createtopic = false,
        $createmenu = true,
        $postcomment = false,
        $section_id,
        $parent_id = false,
        $topic_id = false
    );
  

} else {

    display_navigationBar(
        $topicleap = true,
        $whosonline = true,
        $mainmenu = false,
        $examineuser = true,
        $returntosection = false,
        $createtopic = false,
        $createmenu = false,
        $postcomment = false,
        $section_id,
        $parent_id = false,
        $topic_id = false
    );

}

page_end($breadcrumbs, $t);
