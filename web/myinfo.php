<?php
// select user to examine 

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

$select_code = '';

$db = opendata();


$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION['my_theme']; 

// check login
if (!validlogin())
{
  eject_user();	
}

$user_array = get_user_array($_SESSION['current_id']);

$breadcrumbs = get_dummybreadcrumbs();

$t = new Template($template_location);

display_header($t,
	       $breadcrumbs,
	       'Select User',
	       $user_array['user_name'],
	       $user_array['user_popname'],
	       $_SESSION['current_id'],
	       count_instant_messages($_SESSION['current_id']),
	       SYSOP_ID,
	       SYSOP_NAME,
	       get_count_unread_comments($_SESSION['current_id']),
	       get_count_unread_messages($_SESSION['current_id']));


//update user activity
update_location("Examine User");

#get array of all user_ids and names
$userlist_array = array();
$userlist_array = get_userlist_array();

display_navigationBar(	
		      $topicleap=true,			
		      $whosonline=true,
		      $mainmenu=false,
		      $examineuser=false,
		      $returntosection=false,
		      
		      $createtopic=false,
		      $createmenu=false,
                      $postcomment=false,
		      
		      		      
		      $section_id=false,
		      $parent_id=false,
		      $topic_id=false
		      );

$t->set_file("choose_user", "choose_user.html");


foreach ($userlist_array  as $current_element)
{
  $select_code = $select_code.'<option value="'.
    $current_element['user_id'].'">'.$current_element['user_name'].'</option>';
}

$t->set_var("SELECT_CODE",$select_code);

$t->pparse("MyFinalOutput","choose_user");  

display_navigationBar(	
		      $topicleap=true,			
		      $whosonline=true,
		      $mainmenu=false,
		      $examineuser=false,
		      $returntosection=false,
		      
		      $createtopic=false,
		      $createmenu=false,
                      $postcomment=false,
		      
		      		      
		      $section_id=false,
		      $parent_id=false,
		      $topic_id=false
		      );

page_end($breadcrumbs,$t);
?>
