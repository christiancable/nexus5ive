<?php


include('../includes/theme.php');
include('../includes/database.php');
$db = opendata();


$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$my_theme; 

// check login
if (!validlogin()){
	eject_user();	
}

$user_array = get_user_array($_SESSION[current_id]);

$breadcrumbs = '<font size="-1"><a href="section.php?section_id=1">Main Menu</a> -&gt;</font>';

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

$t->set_var("pagetitle","Select User");
$t->pparse("something","Header");

//update user activity
update_location("Examine User");

#get array of all user_ids and names
$userlist_array = array();
$userlist_array = get_userlist_array();

$t->set_file("choose_user", "choose_user.html");

foreach ($userlist_array  as $current_element){
	$select_code = $select_code.'<option value="'.$current_element[user_id].'">'.$current_element[user_name].'</option>';
}
$t->set_var("SELECT_CODE",$select_code);
  
$t->pparse("MyFinalOutput","choose_user");  
page_end($breadcrumbs);

?>
