<?php
/* 
add a new user - interface
- cfcable july 2003
*/

//includes
include('../includes/theme.php');
include('../includes/database.php');


//common stuff
$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$my_theme; 

// check login
if (!validlogin()){
	eject_user();	
}



$user_array = get_user_array($_SESSION[current_id]);

$users_on_array = get_users_online($_SESSION[current_id], true);

// is the current user a sysop
if($user_array[user_sysop]<>'y')
	header("Location: http://".$_SERVER[HTTP_HOST]."/section.php?section_id=1");

// update this once the utilities menu is online
$breadcrumbs = '<font size="-1"><a href="section.php?section_id=1">Main Menu</a> -&gt; </font>';

$t = new Template($template_location);

display_header($t,
	       $breadcrumbs,
	       "Create User",
	       $user_array["user_name"],
	       $user_array["user_popname"],
	       $_SESSION[current_id],
	       count_instant_messages($_SESSION[current_id]),
	       SYSOP_ID,
	       SYSOP_NAME,
	       get_count_unread_comments($_SESSION[current_id]),
	       get_count_unread_messages($_SESSION[current_id]));


//update user activity
update_location("Create User");

$t->set_file("createuser", "adduser.html");


$t->pparse("MyFinalOutput","createuser");

page_end($breadcrumbs, $t);
?>






