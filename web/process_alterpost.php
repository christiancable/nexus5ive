<?php
/**********************************************/
/* process_alterpost.php                      */
/*                                            */
/* backend to alterpost.php                   */
/**********************************************/

// includes
include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');

//parameters
$message_id = $_POST['message_id'];

$db = opendata();
session_start();

// check login
if (!validlogin()) {
    eject_user();
}

if (!$user_array = get_user_array($_SESSION['current_id'])) {
    nexus_error();
}



if (!$message_array = get_message($message_id)) {
    // check to see if the message exists
    // no such message
    header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
    exit();
} else {
    // topic exists
    // get the topic id here
    if (!$topic_array = get_topic($message_array['topic_id'])) {
        header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
        exit();
    }

    # can user edit post begin
    # need to get section array here

    if (!$section_array = get_section($topic_array['section_id'])) {
        nexus_error();
    }

    if (!can_user_edit_post(
        $user_array['user_id'],
        $user_array['user_sysop'],
        $section_array['user_id'],
        $message_array['user_id'],
        $message_array['message_time']
    )) {
        // topic exists but user can not edit it
        header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=$topic_array[section_id]");
        exit();
    }

    // at this point the current user can edit the message

    # can user edit post end

    $comment = $HTTP_POST_VARS['comment'];
    $subject = $HTTP_POST_VARS['subject'];
    $tempsubject = htmlspecialchars($subject, ENT_QUOTES);
    $subject = $tempsubject;

    if ($HTTP_POST_VARS['allowhtml']) {
        $comment = $comment;
    } else {
        $tempmessage = htmlspecialchars($comment, ENT_QUOTES);
        $comment = $tempmessage;
    }

    $message_array['text'] = $comment;
    $message_array['message_title'] = $subject;

    // make a note of who edited this message last
    $message_array['update_user_id']=$user_array['user_id'];

    //  $message_array['message_popname']=htmlspecialchars($message_array['message_popname'],ENT_QUOTES);

    // unset the time so we can tell the post has been updated
    unset ($message_array['message_time']);

    // update
    if (update_message($message_array)) {
        //worked
    } else {
    
    
    }
  
    header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."readtopic.php?topic_id=".$topic_array['topic_id']);
    exit();
}
