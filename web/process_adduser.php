<?php
/* 
add a new user - interface
- cfcable july 2003
*/

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


//common stuff
$db = opendata();
session_start();

// check login
if (!validlogin()){
	eject_user();	
}



$user_array = get_user_array($_SESSION[current_id]);

// is the current user a sysop
if($user_array[user_sysop]<>'y')
	header("Location: http://".$_SERVER[HTTP_HOST].get_bbsroot()."section.php?section_id=1");

$username =  htmlspecialchars($HTTP_POST_VARS[username],ENT_QUOTES);;
$realname = htmlspecialchars($HTTP_POST_VARS[realname],ENT_QUOTES);
$email = htmlspecialchars($HTTP_POST_VARS[email],ENT_QUOTES);

if ($HTTP_POST_VARS[random] = 'yes'){
// make random password
	$password = mkPasswd();
	$password = htmlspecialchars( $password,ENT_QUOTES);
} else {
	$password =htmlspecialchars( $HTTP_POST_VARS[password],ENT_QUOTES);
}

// call add user function here

// check for existing user accounts with same email or username

$new_user_array = array();

$new_user_array = new_user_array();

$new_user_array[user_name] = $username;
$new_user_array[user_realname] = $realname;
$new_user_array[user_email] = $email;
$new_user_array[user_password] = $password;

echo "$new_user_array[user_name]";
echo "$new_user_array[user_realname]";
echo "$new_user_array[user_email]";
echo "$new_user_array[user_password]";



#DEBUG
exit();

if (add_user($new_user_array)){
	header("Location: http://".$_SERVER[HTTP_HOST].get_bbsroot()."section.php?section_id=1"); 	
} else {
	echo "<h1>bobobo</h1>";
}


exit();
	
?>






