<?php
/* 

displays users who are currently using nexus

*/

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


//common stuff
$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION['my_theme']; 

// check login
if (!validlogin())
{
  eject_user();	
}

//update user activity



$user_array = get_user_array($_SESSION['current_id']);

update_location("Who's Online");

$users_on_array = get_users_online($_SESSION['current_id'], true);

$breadcrumbs = get_dummybreadcrumbs();

$t = new Template($template_location);

display_header($t,
	       $breadcrumbs,
	       "Who's Online",
	       $user_array['user_name'],
	       $user_array['user_popname'],
	       $_SESSION['current_id'],
	       count_instant_messages($_SESSION['current_id']),
	       SYSOP_ID,
	       SYSOP_NAME,
	       get_count_unread_comments($_SESSION['current_id']),
	       get_count_unread_messages($_SESSION['current_id']));

$num_of_users = count($users_on_array);

if($num_of_users)
{
  
  $t->set_file("userson", "userson.html");
  $t->set_var("bbs_name", BBS_NAME);
  $t->set_var("num_of_users",$num_of_users);
  $t->set_var("php_self",$_SERVER['PHP_SELF']);
  switch ($num_of_users)
    {
    case 1:
      $t->set_var("num_of_users", "$num_of_users user is ");
      break;
    default:
      $t->set_var("num_of_users", "$num_of_users users are ");
      break;
    }
  
  $t->set_block('userson', 'UserBlock', 'tablerow'); 
  
  foreach ($users_on_array  as $current_user_array)
    {
      $current_user = get_user_array($current_user_array['user_id']);
      $t->set_var("user_id",$current_user['user_id']);
      $t->set_var("user_popname",$current_user['user_popname']);
      $t->set_var("user_name",$current_user['user_name']);	
      $t->set_var("user_location",$current_user['user_location']);
      
      $idletime = sprintf("%02dm %02ds",$current_user_array['minutes'],$current_user_array['seconds']);
      $t->set_var("user_idle", $idletime);
      $t->parse('tablerow', 'UserBlock', true); 	
      
    }
  
  $t->pparse("MyFinalOutput","userson");
} 
else
{
  // this would indicate that no one was on nexus. which considering the current users is 
  // could be bad
}

page_end($breadcrumbs,$t);
?>






