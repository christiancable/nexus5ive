<?php
# leaps to the next section containg unread messages or to main menu


include('../includes/database.php');

$db = opendata();
session_start();

// check login
if (!validlogin()){
	eject_user();	
}

unsubscribe_from_topic($topic_id, $_SESSION[current_id]);

header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section=$section_id");
?>






