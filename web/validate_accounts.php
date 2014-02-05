<?php

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

// parameters

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
  nexus_error();
}


// only sysops can approve accounts
if($user_array['user_sysop'] != 'y')
{
  header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().
	 "section.php?section_id=1");
}


$breadcrumbs = get_dummybreadcrumbs();

$num_msg = count_instant_messages($_SESSION['current_id']);

display_header($t,
	       $breadcrumbs,
	       "Approve Accounts",
	       $user_array['user_name'],
	       $user_array['user_popname'],
	       $_SESSION['current_id'],
	       $num_msg,
	       SYSOP_ID,
	       SYSOP_NAME,
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

update_location('Approve Accounts');

$invalid_users_array = get_invalid_userlist_array();


if($invalid_users_array)
{
  // show the messages we do have
  
  $t->set_file('users','validate_accounts.html');
  $t->set_block('users', 'UserBlock', 'tablerow'); 

  foreach ($invalid_users_array as $current_user_array)
    {

      $t->set_var("user_id", $current_user_array['user_id']);
      $t->set_var("user_name",$current_user_array['user_name']);
      $t->set_var("user_email", $current_user_array['user_email']);
      $t->set_var("user_ipaddress",$current_user_array['user_ipaddress']);
      $t->parse('tablerow', 'UserBlock', true); 	
      
    }
  $t->pparse('messageoutput','users');	
  
}
else
{
  // show the no waiting accounts
  $t->set_file("accounts","validate_accounts_empty.html");
  $t->pparse("messageoutput","accounts");
}

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
?>
