<?php
// remove topic 

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


// parameters
$topic_id = $_GET['topic_id'];

$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION['my_theme']; 
$t = new Template($template_location);


// check login
if (!validlogin())
{
  eject_user();	
}


if(!$user_array = get_user_array($_SESSION['current_id']))
{
  show_error($_SERVER['PHP_SELF']."get_user_array failed<br>SESSION is ".print_r($_SESSION,true));
}

// can_user_edit fuction here

if (!$topic_array = get_topic($topic_id))
{
  // no such topic
  header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");
  exit();  	
} 
else
{
  // topic exists 
  if(!can_user_edit_topic($user_array, $topic_array))
    {
      // topic exists but user can not edit it
      header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().
	     "section.php?section_id=".$topic_array['section_id']);
      exit();
    }
  
  // at this point the current user can edit the section 
  
  //section owner info
  
  // this is just a simple username look up
  $section_array = get_section($topic_array['section_id']);
  
  if(!$moderator_name = get_username($section_array['user_id']))
    {
      show_error($_SERVER['PHP_SELF']."get_user_array failed<br>SESSION is ".print_r($_SESSION,true));
    }
  
  if(!$breadcrumbs = get_breadcrumbs_topic($section_array['section_id']))
    {
      show_error($_SERVER['PHP_SELF']."get_breadcrumbs_topic failed <br>section_array is "
		 .print_r($section_array, true));
    }
  
  // show header
  display_header($t,
		 $breadcrumbs,
		 'Delete "'.$topic_array['topic_title'].'"',
		 $user_array['user_name'],
		 $user_array['user_popname'],
		 $_SESSION['current_id'],
		 count_instant_messages($_SESSION['current_id']),
		 $section_array['user_id'],
		 $moderator_name,
		 get_count_unread_comments($_SESSION['current_id']),
		 get_count_unread_messages($_SESSION['current_id']));

  display_navigationBar(
			$topicleap=true,
			$whosonline=true,
			$mainmenu=false,
			$examineuser=true,
			$returntosection=false,
			
			$createtopic=false,
			$createmenu=false,
			$postcomment=false,
			
			$section_id=false,
			$parent_id=false,
			$topic_id=false
			);
 
  $t->set_file("topicform","deltopic.html");
  
  $messages_in_topic = get_count_topic_messages($topic_array['topic_id']);
  $t->set_var("POST", $messages_in_topic);
  
  $t->set_var("TOPIC_NAME",$topic_array['topic_title']);
  $t->set_var("SECTION_ID",$topic_array['section_id']);
    $t->set_var("DESCRIPTION",$topic_array['topic_description']);
  $t->set_var("TOPIC_ID",$topic_array['topic_id']);
  
  
  $t->pparse("TopicOutput","topicform");	

  display_navigationBar(
			$topicleap=true,
			$whosonline=true,
			$mainmenu=false,
			$examineuser=true,
			$returntosection=false,
			
			$createtopic=false,
			$createmenu=false,
			$postcomment=false,
			
			$section_id=false,
			$parent_id=false,
			$topic_id=false
			);
  
  page_end($breadcrumbs,$t);
}
?>
