<?php

/**********************************************/
/* leave_user_comment.php                     */
/*                                            */
/* leave a comment about a user for others to */
/* read                                       */
/**********************************************/

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');

$db = opendata();
session_start();

// check login
if (!validlogin())
{
  eject_user();	
}


$instant_message = array();

$instant_message['text']= escape_input($_POST['comment']);
$instant_message['from_id'] = $_SESSION['current_id'];
$instant_message['user_id'] = $_POST['user_id']; # should I check this guy exists or do I trust the form,
  // a missing user should come back as a fail from add_user_comment 

add_user_comment($instant_message);

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().
       "show_userinfo.php?user_id=".$HTTP_POST_VARS['user_id']);


?>