<?php
include('../includes/theme.php');
include('../includes/database.php');

$db = opendata();

# get form vars
$username = $HTTP_POST_VARS[username];
$password =  $HTTP_POST_VARS[password];

if($user_array=get_user_array_from_name($username)){

	# check password
	if($user_array[user_password]<>$password){
		#echo "debug password";
		header("Location: http://".$_SERVER['HTTP_HOST']."/login_error.html");
		exit;
	}

	#check banned
	if($user_array[user_banned]<>0){
		$email_to = "sysop@nexus5.org.uk";
		$email_from = "From: nexus@nexus5.org.uk";
		$error_txt = " $username attempt from $_SERVER[REMOTE_ADDR]\n";
		$str = "[" . date("Y/m/d h:i:s", mktime()) . "] " . $error_txt;
	        mail($email_to, "nexus alert", $str, $email_from);
		header("Location: http://".$_SERVER['HTTP_HOST']."/banned.html");
		#echo "debug banned $user_array[user_banned]";
		exit;
	}

	global $current_id;
	$current_id = $user_array[user_id];
	session_register("current_id");
	session_register("my_theme");
	session_register("no_pictures");
	$current_id = $user_array[user_id];
	$my_theme = $user_array[user_theme];
	$no_pictures = $user_array[user_no_pictures];
	// increase number of times on nexus here
	$num_of_visits = $user_array[user_totalvisits]+1;
	setuser_logon($user_array[user_id],$_SERVER[REMOTE_ADDR], $num_of_visits);
	header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section=1");
	exit;

} else {
	# check username and password page
	#echo "final error";
	header("Location: http://".$_SERVER['HTTP_HOST']."/login_error.html");
}
?>

