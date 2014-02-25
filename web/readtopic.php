<?php

/* 
displays a number of posts in a given topic, paginates them accounting to user preferences

*/

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


//parameters 
$topic_id=$_GET['topic_id'];
@$start_message=$_GET['start_message'];

if (!isset($start_message)) {
    $start_message = false;
}

// common stuff
$db = opendata();
session_start();

$template_location = TEMPLATE_HOME . $_SESSION['my_theme'];

// check login
if (!validlogin()) {
    eject_user();
}

// get info
if (!$user_array = get_user_array($_SESSION['current_id'])) {
    nexus_error();
}

if (!$owner_array = get_topic_owner($topic_id)) {
    nexus_error();
}

if (!$topic_array = get_topic($topic_id)) {
    nexus_error();
}

if (!$section_array = get_section($topic_array['section_id'])) {
    nexus_error();
}

$breadcrumbs = get_breadcrumbs_topic($topic_array['section_id']);

$new_msg_total = new_messages_in_topic($topic_array['topic_id'], $user_array['user_id']);

// update user activity
$location_str = '<a href="readtopic.php?&topic_id=' . $topic_array['topic_id'] . '"><i>Reading</i> ' . $topic_array['topic_title'] . '</a>';

update_location($location_str);

## begin logic to see how many messages to display

# start_message is the row index of the message to start at
# this allows links to point to a given message for search results etc

$num_of_messages_to_show = $user_array['user_display'];
// echo $num_of_messages_to_show;

$messages_to_show_array = fetchPostArray($topicID = $topic_id, $numberOfPosts = $num_of_messages_to_show, $startPost = $start_message, $userID = $user_array['user_id']);

// var_dump($messages_to_show_array);
// die();

if ($user_array['user_backwards'] === "y") {
    $messages_to_show_array = array_reverse($messages_to_show_array);
}

// choose what template
$t = new Template($template_location);

// set unknowns
// $t->set_unknowns($unknowns = "keep");

// chose display mode
if ($topic_array['topic_annon'] == 'y') {
    if (is_topic_owner($topic_id, $user_array['user_id'], $db)) {
        // can see
        $mode = "SECRET_OWNER";
    } else {
        // can not see
        // echo "DEBUG: annon<br>";
        $mode = "SECRET_COMMENT";
    }
} else {
    if (is_topic_owner($topic_id, $user_array['user_id'], $db)) {
        // owner
        $mode = "NORMAL_OWNER";
    } else {
        // not owner
        // echo "DEBUG: normal<br>";
        $mode = "NORMAL_COMMENT";
    }
}

// Topic Title
// BEGIN DISPLAY TOPIC TITLE

display_header(
    $t,
    $breadcrumbs,
    $topic_array['topic_title'],
    $user_array['user_name'],
    $user_array['user_popname'],
    $_SESSION['current_id'],
    count_instant_messages($_SESSION['current_id']),
    $owner_array['owner_id'],
    $owner_array['owner_name'],
    get_count_unread_comments($_SESSION['current_id']),
    get_count_unread_messages($_SESSION['current_id'])
);

// page_header


// end display of topic title
if (($owner_array['owner_id'] == $_SESSION['current_id']) or (is_sysop($_SESSION['current_id']))) {


    display_navigationBar(
        $topicleap = true,
        $whosonline = true,
        $mainmenu = false,
        $examineuser = false,
        $returntosection = true,
        $createtopic = false,
        $createmenu = false,
        $postcomment = true,
        $section_id = false,
        $parent_id = $topic_array['section_id'],
        $topic_id = $topic_id
    );


    $t->set_file('buttons', 'readtopic_links_admin_start.html');
    // SELECT
    $select_code = '<option value="' . $topic_array['topic_id'] . '">' . $topic_array['topic_title'] . '</option>';
    $sectionlist_array = get_topiclist_array($user_array);

    foreach ($sectionlist_array as $current_element) {
        $select_code = $select_code . '<option value="' . $current_element['topic_id'] . '">' . $current_element['topic_title'] . '</option>';
    }

    $t->set_var("SELECT_CODE", $select_code);
    $t->set_var("messages_shown", count($messages_to_show_array));
    $t->set_var("section_id", $topic_array['section_id']);
    $t->set_var("topic_id", $topic_id);
    
    $t->pparse('content', 'buttons');

} else {
  
    if ($topic_array['topic_readonly'] == 'y') {
        display_navigationBar(
            $topicleap = true,
            $whosonline = true,
            $mainmenu = false,
            $examineuser = false,
            $returntosection = true,
            $createtopic = false,
            $createmenu = false,
            $postcomment = false,
            $section_id = false,
            $parent_id = $topic_array['section_id'],
            $topic_id = $topic_id
        );
    } else {
        display_navigationBar(
            $topicleap = true,
            $whosonline = true,
            $mainmenu = false,
            $examineuser = false,
            $returntosection = true,
            $createtopic = false,
            $createmenu = false,
            $postcomment = true,
            $section_id = false,
            $parent_id = $topic_array['section_id'],
            $topic_id = $topic_id
        );

    }
}


// END DISPLAY TOP SET OF BUTTONS
// forward and back
// Create Next / Prev Links and $Result_Set Value

# quick and dirty hack - FIXME

$total_messages = get_count_topic_messages($topic_array['topic_id']);
$new_msg_total = new_messages_in_topic($topic_array['topic_id'], $user_array['user_id']);

if ($start_message === false) {
     $start_message = $total_messages - $user_array['user_display'];

    if ($new_msg_total > $user_array['user_display']) {
        $start_message = $total_messages - $new_msg_total;
        $num_of_messages_to_show = $new_msg_total + 1;
    }
}

echo '<div align="right" class="navigation"><a href="unsub.php?section_id='.$topic_array['section_id'].'&topic_id='.$topic_id.'">[ unsubscribe ]</a></div>';
$browse_html = browse_links($total_messages, $num_of_messages_to_show, $start_message, "readtopic.php", $topic_array['topic_id']);
echo $browse_html;



// echo "showing ".count($messages_to_show_array)." messages";
if (count($messages_to_show_array)) {
    foreach ($messages_to_show_array as $current_message_id) {
        $current_message_array = get_message_with_time($current_message_id['message_id']);

        if (can_user_edit_post(
            $user_array['user_id'],
            $user_array['user_sysop'],
            $section_array['user_id'],
            $current_message_array['user_id'],
            $current_message_array['message_time']
        )
      ) {

            if ($mode === "SECRET_COMMENT") {
                $editing_mode = "SECRET_OWNER";
            }

            if ($mode === "NORMAL_COMMENT") {
                $editing_mode = "NORMAL_OWNER";
            }

            if (($user_array['user_id'] <> $section_array['user_id']) and ($user_array['user_sysop'] <> 'y')) {
                $editing_mode = "NORMAL_EDIT";
            }

        }


        if (isset($editing_mode)) {

        } else {

            $editing_mode = $mode;

        }
      
        display_message(
            $current_message_array,
            $user_array['user_id'],
            $t,
            $editing_mode,
            $db
        );
      
        unset($editing_mode);
      
    }
  
    // now update last view time here
    subscribe_to_topic($topic_id, $_SESSION['current_id']);
    set_topicread($_SESSION['current_id'], $topic_id);
} else {
// No messages to display
}

// DISPLAY BOTTOM SET OF BUTTONS
echo $browse_html;


if (($owner_array['owner_id'] === $_SESSION['current_id']) or (is_sysop($_SESSION['current_id']))) {


    display_navigationBar(
        $topicleap = true,
        $whosonline = true,
        $mainmenu = false,
        $examineuser = false,
        $returntosection = true,
        $createtopic = false,
        $createmenu = false,
        $postcomment = true,
        $section_id = false,
        $parent_id = $topic_array['section_id'],
        $topic_id = $topic_id
    );


} else {
    if ($topic_array['topic_readonly'] == 'y') {
        
        display_navigationBar(
            $topicleap = true,
            $whosonline = true,
            $mainmenu = false,
            $examineuser = false,
            $returntosection = true,
            $createtopic = false,
            $createmenu = false,
            $postcomment = false,
            $section_id = false,
            $parent_id = $topic_array['section_id'],
            $topic_id = $topic_id
        );
    } else {

        display_navigationBar(
            $topicleap = true,
            $whosonline = true,
            $mainmenu = false,
            $examineuser = false,
            $returntosection = true,
            $createtopic = false,
            $createmenu = false,
            $postcomment = true,
            $section_id = false,
            $parent_id = $topic_array['section_id'],
            $topic_id = $topic_id
        );

    }
}

page_end($breadcrumbs, $t);
