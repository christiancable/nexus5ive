<?php
/* 

displays users who are currently using nexus

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

$breadcrumbs = '<font size="-1"><a href="section.php?section_id=1">Main Menu</a> -&gt; <a href="show_userinfo.php?user_id='.$_SESSION[current_id].'">Examining '.$user_array[user_name].'</a></font> -&gt;';

$t = new Template($template_location);


if(get_count_unread_messages($_SESSION[current_id])>0){
       $t->set_file("Header","mail_page.html");
} else {
        $t->set_file("Header","page.html");
}


$t->set_var("breadcrumbs",$breadcrumbs);

$t->set_var("user_name",$user_array["user_name"]);
$t->set_var("user_popname",$user_array["user_popname"]);
$t->set_var("user_id",$_SESSION[current_id]);

#this is cheating ....
$t->set_var("owner_id",SYSOP_ID);
$t->set_var("ownername",SYSOP_NAME);
#cheating ends

if ($num_msg = count_instant_messages($_SESSION[current_id])){
	$t->set_var("num_msg",$num_msg);
}else{
	$t->set_var("num_msg","no");
}

$t->set_var("pagetitle","Change Password");
$t->pparse("something","Header");

//update user activity
update_location("Change Password");

if($error){
	
	$error_text = "Password Not Changed!<br><br>please make sure that you type in your current password correctly<br>and that both new passwords match<br>";
	$t->set_var("error_text",$error_text);

}
$t->set_file("change_password", "change_password.html");

$t->pparse("MyFinalOutput","change_password");

page_end($breadcrumbs);
?>






