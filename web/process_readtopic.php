<?php
// backend to readtopic - cfc

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

$message_id_array = $HTTP_POST_VARS[MessChk];
$topic_id = $HTTP_POST_VARS[topic_id];
$dest_topic_id = $HTTP_POST_VARS[select];

if (!$topic_array = get_topic($topic_id)){ 
    # topic does not exist, bounce them to the main menu
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/section.php?section_id=1");
    exit();
}

if (can_user_edit_topic($user_array, $topic_array)){
    if (isset($Delete)){
        if(!delete_messages($message_id_array)){ 
            # echo "delete failed dude";
        }
    }

    if (isset($Move)){
        if(!move_messages($message_id_array, $dest_topic_id)){ 
            # echo "move failed"
        }
    } 
    # redirect back to readtopic
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/readtopic.php?section_id=$topic_array[section_id]&topic_id=$topic_array[topic_id]");
    exit();
}else{ 
    # user can not edit this topic, bounce them to read topic
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/readtopic.php?section_id=$topic_array[section_id]&topic_id=$topic_array[topic_id]");
    exit();
}

?>
