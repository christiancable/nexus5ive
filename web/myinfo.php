<?php



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

display_header($t,
	       $breadcrumbs,
	       'Select User',
	       $user_array["user_name"],
	       $user_array["user_popname"],
	       $_SESSION[current_id],
	       count_instant_messages($_SESSION[current_id]),
	       SYSOP_ID,
	       SYSOP_NAME,
	       get_count_unread_comments($_SESSION[current_id]),
	       get_count_unread_messages($_SESSION[current_id]));


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
page_end($breadcrumbs,$t);

?>
