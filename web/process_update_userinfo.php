<?php
// show_userinfo
// accepts user_id as a argument
// displays info in a readonly format

//includes
include('../includes/theme.php');
include('../includes/database.php');

// function 

//common stuff

$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$my_theme; 

// check login
if (!validlogin()){
	eject_user();	
}

// check if user exists if not check them to the main menu

#if (!$examine_user_array = get_user_array($user_id)) {
#    header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
#    exit();
#}

$user_array = get_user_array($_SESSION[current_id]);

# update the array with form values

#echo "<h1>test bollox</h1>";
#echo "<br><pre>";

# --- use mysql special chars here and <br> to line breaks

# translate line breaks

# quote mysql things

# quote html



$user_array[user_realname] = escape_input($user_realname);       
$user_array[user_email] = escape_input($user_email);
$user_array[user_popname] = escape_input($user_popname);
$user_array[user_sex] = escape_input($user_sex);
$user_array[user_display] = escape_input($show_post);
$user_array[user_location] = escape_input($user_array[user_location]);
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

/*
echo "Topic Depth: ".escape_input($show_post)."\n";
echo "No Pictures: ".escape_input($pictures)."\n";
echo "Backwards: ".escape_input($backwards)."\n";

echo "Fave Film: ".escape_input($user_film)."\n";
echo "Fave Film: ".$user_array[user_film]."\n";

echo "Fave Band: ".escape_input($user_band)."\n";
echo "Town: ".escape_input($user_town)."\n";
echo "Age: ".escape_input($user_age)."\n";

#echo "Comment: ".escape_input($user_comment)."\n";
*/

update_user_array($user_array);

#echo "debug: pictures is set to: $user_array[user_no_pictures]";
header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
exit();
