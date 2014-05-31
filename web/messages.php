<?php

namespace nexusfive;

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

use Template;

include_once('../includes/nxdata.php');
include_once('../includes/nxinterface.php');
include_once('../includes/nxconfig.php');

// parameters
if (isset($_GET['sendtoid'])) {
    $sendtoid = $_GET['sendtoid'];
}


$configuration = new NxConfig();
$userinterface = new NxInterface();
$datastore = new NxData($configuration->getConfig());


session_start();

$template_location =TEMPLATE_HOME.$_SESSION['my_theme'];
$t = new Template($template_location);

if (isset($_SESSION['current_id'])) {
    $datastore->updateLastActiveTime($_SESSION['current_id']);
} else {
    eject_user();
}

if (!$user_array = $datastore->readUserInfo($_SESSION['current_id'])) {
    nexus_error();
}


$top_section_info = $datastore->readSectionInfo(1);

$breadcrumbs = $userinterface->getBreadcrumbs($top_section_info);

$instant_message_array = $datastore->readInstantMessages($_SESSION['current_id']);

if ($instant_message_array === false) {
    nexus_error();
} else {
    $num_msg = count($instant_message_array);
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
    $datastore->countComments($_SESSION['current_id']),
    false
);

$datastore->updateUserLocation($_SESSION['current_id'], 'Instant Messages');


$other_users_array = $datastore->readOnlineUsers($_SESSION['current_id'], true);


$db = opendata();
//if other users on give them the send template
$users_on_array = array();





/* this function returns an array of other users online to populate the send to box */
if (!$users_on_array = get_users_online($_SESSION['current_id'], true)) {
  
} else {
  
}

// var_dump($users_on_array);
// var_dump($other_users_array);


// echo "<pre>".print_r($users_on_array,true)."</pre>";


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

    $select_code = '';
    foreach ($users_on_array as $current_user_array) {
        $select_code = $select_code."\n".'<option value="'.$current_user_array['user_id'].'"';

        if (isset($sendtoid)) {
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
