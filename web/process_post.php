<?php
# add post
# backend to post.php
# should do no output to the screen here

$topic_id = $_POST['topic_id'];
$comment =  $_POST['comment'];
$subject =  $_POST['subject'];


// includes

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

if(!$user_array = get_user_array($_SESSION['current_id']))
{
  show_error($_SERVER['PHP_SELF'].'get_user_array failed<br>SESSION is '.print_r($_SESSION,true));
}

// can_user_edit fuction here

###NEED TO GET THE TOPIC ID FROM SOMEWHERE FIRST!

if (!$topic_array = get_topic($topic_id))
{
  // no such topic
  show_error($_SERVER['PHP_SELF']." no such topic: topic_id = $topic_id, user_id=".
	     $user_array['user_id'].'<br>'.print_r($_POST,true)."<br>".print_r($HTTP_POST_VARS,true));
} 
else
{
  
  if(!can_user_add_message($user_array, $topic_array))
    {
      show_error($_SERVER['PHP_SELF']."user not allowed to post: topic_id = $topic_id, user_id=".$user_array['user_id']);
    }
  
  // if not section owner or sysop strip out any html
  
  // subject and comment HTML VARS
  
  $tempsubject = htmlspecialchars($subject,ENT_QUOTES);
  //  $subject = nl2br($tempsubject);
  $subject = $tempsubject;
  
  
  if( is_section_owner($topic_array['section_id'],$user_array['user_id'],$db) )
    {
      // if we have the privs use html   
      // $comment = nl2br($comment);
    } 
  else 
    {
      // strip out html replace line endings
      $tempmessage = htmlspecialchars($comment, ENT_QUOTES);
      // $comment = nl2br($tempmessage);
      $comment = $tempmessage;
    }

	
  
  // add post
  // array of the format, should I make a blank array somehow here?????
  $popname = $user_array['user_popname'];
  $message_array['message_text']=$comment;
  $message_array['topic_id']=$topic_array['topic_id'];
  $message_array['user_id']=$user_array['user_id'];
  $message_array['message_title']=$subject;
  $message_array['message_popname']=addslashes($popname);
  
  if(!add_message($message_array))
    {
      show_error($_SERVER['PHP_SELF'].'add_message failed:<br/>'.print_r($message_array,true));       
    }
  // update number of edits
  
  inc_total_edits($user_array);
  
  // redirect to readtopic
  header('Location: http://'.$_SERVER['HTTP_HOST'].get_bbsroot().
	 'readtopic.php?topic_id='.$topic_array['topic_id']);
  exit();
  
}
?>
