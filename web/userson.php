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

$breadcrumbs = '<font size="-1"><a href="section.php?section_id=1">Main Menu</a> -&gt; </font>';

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
$t->set_var("owner_id",1);
$t->set_var("ownername",'fraggle');
#cheating ends

if ($num_msg = count_instant_messages($_SESSION[current_id])){
	$t->set_var("num_msg",$num_msg);
}else{
	$t->set_var("num_msg","no");
}

$t->set_var("pagetitle","Who's Online");
$t->pparse("something","Header");

//update user activity
update_location("Who's Online");

$num_of_users = count($users_on_array);

if($num_of_users) {

	$t->set_file("userson", "userson.html");

	$t->set_var("num_of_users",$num_of_users);
	$t->set_var("php_self",$_SERVER['PHP_SELF']);
	switch ($num_of_users) {
		case 1:
	        $t->set_var("num_of_users", "$num_of_users user is ");
	        break;
	    default:
			$t->set_var("num_of_users", "$num_of_users users are ");
	        break;
	}

	$t->set_block('userson', 'UserBlock', 'tablerow'); 
	
	foreach ($users_on_array  as $current_user_array){
		$current_user = get_user_array($current_user_array[user_id]);
   	    $t->set_var("user_id",$current_user["user_id"]);
	    $t->set_var("user_popname",$current_user["user_popname"]);
	    $t->set_var("user_name",$current_user["user_name"]);	
	    $t->set_var("user_location",$current_user["user_location"]);

	    $idletime = sprintf("%02dm %02ds",$current_user_array["minutes"],$current_user_array["seconds"]);
	    $t->set_var("user_idle", $idletime);
	    $t->parse('tablerow', 'UserBlock', true); 	
		
	}
	
	$t->pparse("MyFinalOutput","userson");
} else {
 // this would indicate that no one was on nexus. which considering the current users is 
 // could be bad
}

page_end($breadcrumbs);
?>






