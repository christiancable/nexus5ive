<?php
/* 

sets last viewed dates on all files

*/

//includes

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

//common stuff
$db = opendata();
session_start();

// check login
if (!validlogin()){
	eject_user();	
}

#get rid of current info
delete_previous_topicview($_SESSION[current_id]);

$all_post_date_array = get_latest_post_dates();

# delete from topicview where user_id=$user_id and unsubscribe <> 1

$topicview_array = array();

foreach ($all_post_date_array as $post_date_array){
	$topicview_array[user_id] = $_SESSION[current_id];
	#echo "<br>_$post_date_array[message_time]";
	$topicview_array[message_time] = $post_date_array[message_time];
	$topicview_array[topic_id] = $post_date_array[topic_id];
	add_topicview($topicview_array);
}

# refresh to main menu
header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");

?>






