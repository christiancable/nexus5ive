<?php
#include('../includes/theme.php');
include('../includes/database.php');

$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$my_theme; 
$t = new Template($template_location);


// check login
if (!validlogin()){
	eject_user();	
}

if(!$user_array = get_user_array($_SESSION[current_id])){
	nexus_error();
}



if (get_count_unread_messages($_SESSION[current_id])>0) {
       $t->set_file("header","mail_page.html");
	
	} else {
       $t->set_file("header","page.html");
	}




$breadcrumbs = '<font size="-1"><a href="section.php?section_id=1">Main Menu</a> -&gt; </font>';
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

$t->set_var("pagetitle","Instant Messages");
update_location("Instant Messages");
$t->pparse("output","header");


$refresh_url = 'http://'.$_SERVER[HTTP_HOST].$_SERVER[PHP_SELF];
if($_SERVER[QUERY_STRING])
	$refresh_url = $refresh_url.'?'.$_SERVER[QUERY_STRING];
	
#echo $refresh_url;

if($num_msg){
// show the messages we do have
	# mark messages as read
	
	mark_messages_read($_SESSION[current_id]);
	$t->set_file("messages","messages.html");
	$t->set_block('messages', 'MessageBlock', 'tablerow'); 
	$t->set_var("reload_page",$refresh_url);
	if(!$instant_message_array = get_instant_messages($_SESSION[current_id]))
		echo "danger";
	foreach ($instant_message_array as $current_message_array){
   	    $t->set_var("user_id",$current_message_array[from_id]);
	    $t->set_var("message",$current_message_array[text]); # strip this?
	    $t->set_var("user_name",$current_message_array[user_name]);		  
	    $t->parse('tablerow', 'MessageBlock', true); 	
		
	}
	$t->pparse("messageoutput","messages");	
	


}else{
// show the no message template
	$t->set_file("messages","no_messages.html");
	$t->set_var("reload_page",$refresh_url);
	$t->pparse("messageoutput","messages");
}

//if other users on give them the send template
$user_on_array = array();

if(!$users_on_array = get_users_online($_SESSION[current_id], false)){

}else{

}


#echo "debug: ".count($users_on_array);

if($users_on_array){
	$t->set_file("sendmessages","send_message.html");
	$select_code = "";
	foreach ($users_on_array  as $current_user_array){
		$select_code = $select_code.'<option value="'.$current_user_array[user_id].'"';
		if(isset($sendtoid)) {
				if($sendtoid == $current_user_array[user_id])
					$select_code = $select_code." SELECTED ";
		}			
		$select_code = $select_code.' >'.$current_user_array[user_name].'</option>';
	}
	$t->set_var("select_code",$select_code);
	$t->pparse("sendoutput","sendmessages");
}else{
// no others users online 
	$t->set_file("sendmessages","no_send_message.html");
	$t->pparse("sendoutput","sendmessages");

}
page_end($breadcrumbs);

?>