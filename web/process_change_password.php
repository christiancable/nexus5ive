<?php
/* 

backend for the change password screen

*/

//includes

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

$current_password = $HTTP_POST_VARS[current_password];
$new_password = $HTTP_POST_VARS[new_password];
$new_password2 = $HTTP_POST_VARS[new_password2];


if (($current_password == $user_array[user_password]) and ($new_password == $new_password2)){
    	#echo "good<br>";
	change_password($_SESSION[current_id], $new_password);
	header ("Location: http://".$_SERVER['HTTP_HOST']."/show_userinfo.php?user_id=$_SESSION[current_id]");
} else {
	header ("Location: http://".$_SERVER['HTTP_HOST']."/change_password.php?error=1");
}
	
	
	
?>






