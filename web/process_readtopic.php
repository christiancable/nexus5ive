<?php
// backend to readtopic - cfc

// parameters

$message_id_array = $_POST[MessChk];
$topic_id = $_POST[topic_id];
$dest_topic_id = $_POST[select];
$Delete = $_POST[Delete];
$Move = $_POST[Move];

include('../includes/database.php');

$db = opendata();
session_start();
// check login
if (!validlogin()){
    eject_user();
}

if(!$user_array = get_user_array($_SESSION[current_id])){
    nexus_error();
}

if (!$topic_array = get_topic($topic_id)){ 
    # topic does not exist, bounce them to the main menu
    header("Location: http://" . $_SERVER['HTTP_HOST'] .get_bbsroot()."section.php?section_id=1");
    exit();
}

if (can_user_edit_topic($user_array, $topic_array)){
    if ($Delete=="Delete Posts"){
        if(!delete_messages($message_id_array)){ 
            # echo "delete failed dude";
        }
    }

    if ($Move=="Move"){
        if(!move_messages($message_id_array, $dest_topic_id)){ 
            # echo "move failed"
        }
    } 
    # redirect back to readtopic
    header("Location: http://" . $_SERVER['HTTP_HOST'] .get_bbsroot(). "readtopic.php?section_id=$topic_array[section_id]&topic_id=$topic_array[topic_id]");
    exit();
}else{ 
    # user can not edit this topic, bounce them to read topic
    header("Location: http://" . $_SERVER['HTTP_HOST'] .get_bbsroot()."/readtopic.php?section_id=$topic_array[section_id]&topic_id=$topic_array[topic_id]");
    exit();
}

?>
