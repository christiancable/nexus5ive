<?php
/* 

let user change their password - cfc

*/

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

//parameters
if($_GET[error]){
  $error = $_GET[error];
}
	
//common stuff
$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION[my_theme]; 

// check login
if (!validlogin()){
  eject_user();	
}

$user_array = get_user_array($_SESSION[current_id]);

$users_on_array = get_users_online($_SESSION[current_id], true);

$breadcrumbs = get_dummybreadcrumbs().' <a href="show_userinfo.php?user_id='.$_SESSION[current_id].'">Examining '.$user_array[user_name].'</a></font> -&gt;';

$t = new Template($template_location);

display_header($t,
	       $breadcrumbs,
	       "Change Password",
	       $user_array["user_name"],
	       $user_array["user_popname"],
	       $_SESSION[current_id],
	       count_instant_messages($_SESSION[current_id]),
	       SYSOP_ID,
	       SYSOP_NAME,
	       get_count_unread_comments($_SESSION[current_id]),
	       get_count_unread_messages($_SESSION[current_id]));
  ## message

//update user activity
update_location("Change Password");

if($error){
	
	$error_text = "Password Not Changed!<br><br>please make sure that you type in your current password correctly<br>and that both new passwords match<br>";
	$t->set_var("error_text",$error_text);

}
$t->set_file("change_password", "change_password.html");

$t->pparse("MyFinalOutput","change_password");

page_end($breadcrumbs,$t);
?>






