<?php
// show_userinfo
// accepts user_id as a argument
// displays info in a readonly format

//includes

include('../includes/database.php');

// parameters
$user_realname = $_POST[user_realname];
$user_email = $_POST[user_email];
$user_popname = $_POST[user_popname];
$user_sex = $_POST[user_sex];
$user_display = $_POST[show_post];
$user_town = $_POST[user_town];
$user_film = $_POST[user_film];
$backwards = $_POST[backwards];
$user_band = $_POST[user_band];
$user_age = $_POST[user_age];
$user_comment = $_POST[user_comment];
$pictures = $_POST[pictures];

// function 

//common stuff

$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION[my_theme]; 

// check login
if (!validlogin()){
	eject_user();	
}

// check if user exists if not check them to the main menu


$user_array = get_user_array($_SESSION[current_id]);



$user_array[user_realname] = escape_input($user_realname);       
$user_array[user_email] = escape_input($user_email);
$user_array[user_popname] = escape_input($user_popname);
$user_array[user_sex] = $user_sex;
$user_array[user_display] = $user_display;
$user_array[user_town] = escape_input($user_town);
$user_array[user_film] = escape_input($user_film);


if($pictures){
	$user_array[user_no_pictures] = 'y';
	# update session var
	$_SESSION[no_pictures] = 'y';
} else {
	$user_array[user_no_pictures] = 'n';
	# update session var
	$_SESSION[no_pictures] = 'n';
}


if ($backwards){
	$user_array[user_backwards] = 'y';
} else { 
	$user_array[user_backwards] = 'n';
}
	
$user_array[user_town] = escape_input($user_town);
$user_array[user_band] = escape_input($user_band);
$user_array[user_age] = escape_input($user_age);
$user_array[user_comment] = escape_input($user_comment);


update_user_array($user_array);

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."/section.php?section_id=1");
exit();

?>