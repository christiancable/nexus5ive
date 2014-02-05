<?php
// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

// parameters

$parent_id = $_GET['parent_id'];

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
  show_error($_SERVER['PHP_SELF']."get_user_array failed for moderator <br> SESSION is ".
	     print_r($_SESSION,true));
}

// can_user_edit the parent section

// parent id must come from passed var 

if (!$section_array = get_section($parent_id))
{
  // no such section
  header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().
	 "section.php?section_id=1");
  exit();  	
} 
else
{
  // section exists 
  if(!can_user_edit_section($user_array, $section_array))
    {
      header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().
	     "section.php?section_id=$section_array[section_id]");
      exit();
    }
  
  // at this point the current user can the parent section
  
  
  // this is just a simple username look up
  
  if(!$moderator_name = get_username($section_array['user_id']))
    {
      show_error($_SERVER['PHP_SELF'],"get_user_array failed for moderator <br> SESSION is "
		 .print_r($_SESSION,true));
    }
  
  if(!$breadcrumbs = get_breadcrumbs_topic($section_array['section_id']))
    {
      show_error($_SERVER['PHP_SELF'],"get_breadcrumbs_topic failed <br>"
		 .print_r($section_array, true));
    }
  // why wasn't this checking before?
  
  // show header
  display_header($t,
		 $breadcrumbs,
		 "Create Menu",
		 $user_array['user_name'],
		 $user_array['user_popname'],
		 $_SESSION['current_id'],
		 count_instant_messages($_SESSION['current_id']),
		 $section_array['user_id'],
		 $moderator_name,
		 get_count_unread_comments($_SESSION['current_id']),
		 get_count_unread_messages($_SESSION['current_id']));
  
  
  // show modify section comment
  
  // get create section template
  // fill in section_id
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
			
  $t->set_file("sectionform","create_section.html");
  
  
  $t->set_var("SECTION_ID",$section_array['section_id']);
  
  // SELECT
  $select_code = '<option value="'.$section_array['user_id'].'">'.$moderator_name.'</option>';
  $userlist_array=get_userlist_array();
  foreach ($userlist_array  as $current_element)
    {
      $select_code = $select_code.'<option value="'.$current_element['user_id'].
	'">'.$current_element['user_name'].'</option>';
    }
  
  $t->set_var("SELECT_CODE",$select_code);
  
  $t->pparse("SectionOutput","sectionform");	

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
}
?>
