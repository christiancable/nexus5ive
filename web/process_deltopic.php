<?php 
// back end to deltopic.php - cfc

// parameters
$section_id = $_POST[section_id];
$topic_id = $_POST[topic_id];
$confirm = $_POST[confirm];

include('../includes/database.php');
$db = opendata();

session_start();

if (!validlogin()){
    eject_user();
}

if (!$user_array = get_user_array($_SESSION[current_id])){
    nexus_error();
}

if(!$topic_array = get_topic($topic_id)){
    header("Location: http://" . $_SERVER['HTTP_HOST'] . get_bbsroot(). "section.php?section_id=1");
    exit();
}


if ($confirm == "on"){ 
    // topic exists
    if (!can_user_edit_topic($user_array, $topic_array)){ 
        // topic exists but user can not edit it
        #echo "zero $topic_array[section_id]"; 
         header("Location: http://" . $_SERVER['HTTP_HOST'] . get_bbsroot(). "section.php?section_id=$topic_array[section_id]");
        exit();
    } 
    // at this point the current user can edit the section
    if (!delete_topic($topic_id)){ 
        // topic not removed
        #echo "one $topic_array[section_id]";
      nexus_error();
    }else{ 
        // topic removed so reidrect to the parent section
        #echo "two $topic_array[section_id]"; 
        header("Location: http://" . $_SERVER['HTTP_HOST'] . get_bbsroot(). "section.php?section_id=$topic_array[section_id]");
        exit();
    }
}else{
#echo "three $topic_array[section_id]"; 
header("Location: http://" . $_SERVER['HTTP_HOST'] . get_bbsroot() . "section.php?section_id=$topic_array[section_id]");
exit();
}

?>
