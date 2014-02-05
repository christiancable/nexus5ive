<?php
// backend to readtopic - cfc
// rewritten Dec 2004 

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');

$topic_id = $_POST['topic_id'];
$dest_topic_id = $_POST['select'];
$action = false;
$message_id_array = false;

if (isset($_POST['Delete']))
{
  $action = 'delete';
}

if (isset($_POST['Move']))
{
  $action = 'move';
}

if (isset($_POST['HTML']))
{
  $action = 'html';
}

if (isset($_POST['MessChk']))
{
  $message_id_array = $_POST['MessChk'];
}


$db = opendata();
session_start();

// check login
if (!validlogin())
{
  eject_user();
}

if(!$user_array = get_user_array($_SESSION['current_id']))
{
  nexus_error();
}

if (!$topic_array = get_topic($topic_id))
{
  // topic does not exist, bounce them to the main menu
  header('Location: http://' . $_SERVER['HTTP_HOST'] .get_bbsroot()
	 .'section.php?section_id=1');
  exit();
}


if (can_user_edit_topic($user_array, $topic_array))
{

  switch ($action)
    {
    case "delete":
      if(!delete_messages($message_id_array))
	{
	  // display error about deleting the messages
	}
      break;
      
    case "move":
      if (!move_messages($message_id_array, $dest_topic_id))
	{
	  // display error about not moving the messages
	}
      break;
      
    case "html":
      if (!html_messages($message_id_array))
        {
          // display error about not htmling the messages
        }
      break;
      
    default:
      break;
      
    }
}
    else 
{
  // user can not edit the topic so we do nothing
}

header('Location: http://' . $_SERVER['HTTP_HOST'] .get_bbsroot().
       '/readtopic.php?topic_id='.$topic_array['topic_id']);
exit();


?>
