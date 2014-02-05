<?php

/**********************************************/
/* change_password.php                        */
/*                                            */
/* allow the user to change password          */
/*                                            */
/* called from show_userinfo.pph              */
/**********************************************/

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

//parameters
if(isset($_GET['error']))
{
  $error = $_GET['error'];
}
else
{
  $error = false;

}	

//common stuff
$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION['my_theme']; 

// check login
if (!validlogin())
{
  eject_user();	
}

$user_array = get_user_array($_SESSION['current_id']);

$users_on_array = get_users_online($_SESSION['current_id'], true);

$breadcrumbs = get_dummybreadcrumbs().' <a href="show_userinfo.php?user_id='.
$_SESSION['current_id'].'">Examining '.$user_array['user_name'].'</a></font> -&gt;';

$t = new Template($template_location);

display_header($t,
	       $breadcrumbs,
	       "Change Password",
	       $user_array['user_name'],
	       $user_array['user_popname'],
	       $_SESSION['current_id'],
	       count_instant_messages($_SESSION['current_id']),
	       SYSOP_ID,
	       SYSOP_NAME,
	       get_count_unread_comments($_SESSION['current_id']),
	       get_count_unread_messages($_SESSION['current_id']));

update_location("Change Password");

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

if($error)
{
  
  $error_text = "<b>Password Not Changed!</b><br><br>please make sure that you type in your current password correctly<br>and that both new passwords match<br>";
  $t->set_var("error_text",$error_text);
  
}

$t->set_var("user_id", $user_array['user_id']);
$t->set_file("change_password", "change_password.html");
$t->pparse("MyFinalOutput","change_password");

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






