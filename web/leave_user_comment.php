<?php

include('../includes/database.php');

$db = opendata();
session_start();

// check login
if (!validlogin()){
	eject_user();	
}

# delete messages

#delete_instant_messages($_SESSION[current_id]);

$instant_message = array();
#get post vars
$instant_message[text]= escape_input($HTTP_POST_VARS[comment]);
$instant_message[from_id] = $_SESSION[current_id];
$instant_message[user_id] = $HTTP_POST_VARS[user_id]; # should I check this guy exists or do I trust the form

add_user_comment($instant_message);

/*
echo "DEBUG<br>";
echo "from_id: $instant_message[from_id]<br>";
echo "user_id: $instant_message[user_id]<br>";
echo "text: $instant_message[text]<br>";
echo "<br><br>";
#echo "<a href=\"show_userinfo.php?user_id=".$instant_message[user_id].'"> continue </a>';
*/
header("Location: http://".$_SERVER['HTTP_HOST']."/show_userinfo.php?user_id=".$HTTP_POST_VARS[user_id]);


?>