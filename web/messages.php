<?php

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

// parameters

if (isset($_GET['sendtoid'])) {
    $sendtoid = $_GET['sendtoid'];
}

$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION['my_theme'];
$t = new Template($template_location);



// check login
if (!validlogin()) {
    eject_user();
}

if (!$user_array = get_user_array($_SESSION['current_id'])) {
    nexus_error();
}


$breadcrumbs = get_dummybreadcrumbs();

if ($num_msg = count_instant_messages($_SESSION['current_id'])) {
    if (!$instant_message_array = get_instant_messages($_SESSION['current_id'])) {
        echo "danger";
    }
}

display_header(
    $t,
    $breadcrumbs,
    "Instant Messages",
    $user_array['user_name'],
    $user_array['user_popname'],
    $_SESSION['current_id'],
    $num_msg,
    SYSOP_ID,
    SYSOP_NAME,
    get_count_unread_comments($_SESSION['current_id']),
    false
);

update_location('Instant Messages');

//if other users on give them the send template
$user_on_array = array();

// CFC - 07/02/2014 - wtf is happening here!
if (!$users_on_array = get_users_online($_SESSION['current_id'], false)) {
  
} else {
  
}


if ($users_on_array) {

    // display navigationmenu
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

    $t->set_file("sendmessages", "send_message.html");

    foreach ($users_on_array as $current_user_array) {
        $select_code = $select_code."\n".'<option value="'.$current_user_array['user_id'].'"';

        if ($sendtoid) {
          // if we are following a reply to link

            if ($sendtoid == $current_user_array['user_id']) {
                $select_code = $select_code." SELECTED ";
            }
        } else {
            if (isset($instant_message_array[0]['from_id'])) {
                // if we have a message to reply to
                if ($instant_message_array[0]['from_id'] == $current_user_array['user_id']) {
                    $select_code = $select_code." SELECTED ";
                }
            }
        }
        $select_code = $select_code.' >'.$current_user_array['user_name'].'</option>';
      
    }

    // add send to everyone option
    $select_code = $select_code."\n".'<option value="ALL">*Everyone*</option>';
    $t->set_var("select_code", $select_code);
    $t->pparse("sendoutput", "sendmessages");
} else {

    //  no others users online
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
}


if ($num_msg) {
    // show the messages we do have

    // mark messages as read
    mark_messages_read($_SESSION['current_id']);

    $t->set_file("messages", "messages.html");
    $t->set_block('messages', 'MessageBlock', 'tablerow');


    foreach ($instant_message_array as $current_message_array) {
        $t->set_var("message_id", $current_message_array['nexusmessage_id']);
        $t->set_var("user_id", $current_message_array['from_id']);
        $t->set_var("message", $current_message_array['text']);
        $t->set_var("user_name", $current_message_array['user_name']);
        $t->parse('tablerow', 'MessageBlock', true);

    }

    $t->pparse("messageoutput", "messages");
  
} else {
    // show the no message template
    $t->set_file("messages", "no_messages.html");
    $t->pparse("messageoutput", "messages");
}

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
