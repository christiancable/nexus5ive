<?php
// show_userinfo
// accepts user_id as a argument
// displays info in a readonly format

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

if($HTTP_POST_VARS[user_id])
 $user_id=$HTTP_POST_VARS[user_id];

// check if user exists if not check them to the main menu
if (!$examine_user_array = get_user_array($user_id)) {
    header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section_id=1");
    exit();
}


$user_array = get_user_array($_SESSION[current_id]);

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
$t->set_var("owner_id",SYSOP_ID);
$t->set_var("ownername",SYSOP_NAME);
#cheating ends

if ($num_msg = count_instant_messages($_SESSION[current_id])){
	$t->set_var("num_msg",$num_msg);
}else{
	$t->set_var("num_msg","no");
}

$t->set_var("pagetitle","Examining $examine_user_array[user_name]");
$t->pparse("something","Header");

#get array of all user_ids and names
$userlist_array = array();
$userlist_array = get_userlist_array();


//update user activity
update_location('<a href="show_userinfo.php?user_id='.$examine_user_array[user_id].'">Examining '.$examine_user_array[user_name].'</a>');



## main user_info 
if($examine_user_array[user_id]==$user_array[user_id]){
	$t->set_file("userinfo", "alter_userinfo.html");
} else {
	$t->set_file("userinfo", "show_userinfo.html");
}


#find examined user in the userlist_array
$user_index=0;
foreach($userlist_array as $userlist_entry) {
    #print " ".$userlist_entry[user_name]."-";
    if($userlist_entry[user_id] == $examine_user_array[user_id])
    	break;
 
   $user_index++;
}


$t->set_var("examine_user_id", $user_index+1);
$next_id = $user_index+1;
$pre_id = $user_index-1;

echo "<!-- index is $user_index, prev is $pre_id and next is $next_id -->\n";
$t->set_var("previous_user_id", $userlist_array[$pre_id][user_id]);
$t->set_var("next_user_id", $userlist_array[$next_id][user_id]);

if($userlist_array[$next_id][user_id])
	$t->set_var("next_user_name","[ Next &gt;&gt; ]"); 
else
	$t->set_var("next_user_name",""); 
	
if($userlist_array[$pre_id][user_id])	
	$t->set_var("previous_user_name","[ &lt;&lt; Previous ]"); 
else
	$t->set_var("previous_user_name",""); 

$t->set_var("total_users", count($userlist_array));

$t->set_var("user_name",$examine_user_array[user_name]);
$t->set_var("user_realname",$examine_user_array[user_realname]);
$t->set_var("user_motto","$examine_user_array[user_popname]");

#SOMETHINGS LOOK DIFFERENT WHEN EXAMINING YOURSELF
if($examine_user_array[user_id]==$user_array[user_id]){
	#mark comments as read
	mark_comments_read($user_array[user_id]);

	#alter user_info
	
	$t->set_var("user_email",$examine_user_array[user_email]);
	$t->set_var("user_comment",ereg_replace("<br />","",($examine_user_array[user_comment])));
	
	
	// backwards
	if($examine_user_array[user_backwards] == 'y'){
		$t->set_var("backwards_checked","checked");
	} else {
		$t->set_var("backwards_checked","");
	}
	// pictures
	if($examine_user_array[user_no_pictures] == 'y'){
		$t->set_var("pictures_checked","checked");
	} else {
		$t->set_var("pictures_checked","");
	}
	// topic depth
	
	switch ($examine_user_array[user_display]) {
	
		
		case 5:
			$t->set_var("show_5","selected");
			break;
		case 10:
			$t->set_var("show_10","selected");
			break;
		case 15:
			$t->set_var("show_15","selected");
			break;
		case 20:
			$t->set_var("show_20","selected");
			break;
		case 25:
			$t->set_var("show_25","selected");
			break;
		case 30:
			$t->set_var("show_30","selected");
			break;
		case 40:
			$t->set_var("show_40","selected");
			break;
		case 50:
			$t->set_var("show_50","selected");
			break;
		case 100:
			$t->set_var("show_100","selected");
			break;
		case 150:
			$t->set_var("show_150","selected");
			break;
		case 255:
			$t->set_var("show_all","selected");
			break;
					
		default:
			$t->set_var("show_10","selected");
	}
	
	// sex
	switch ($examine_user_array[user_sex]) {
	
		case "male":
			$t->set_var("sex_boy","selected");
			$t->set_var("sex_girl","");
			$t->set_var("sex_unknown","");
			break;
		case "female":
			$t->set_var("sex_boy","");
			$t->set_var("sex_girl","selected");
			$t->set_var("sex_unknown","");
			break;
		
		default:
			$t->set_var("sex_boy","");
			$t->set_var("sex_girl","");
			$t->set_var("sex_unknown","selected");
	}
	
	
	
} else {
	# view user info
	$t->set_var("user_comment",nx_code($examine_user_array[user_comment]));
	$t->set_var("user_email",'<a href="mailto:'.$examine_user_array[user_email].'">'.$examine_user_array[user_email].'</a>');
	$examine_user_array[user_sysop]='n';

	if($sectionlist_array=get_sectionlist_array($examine_user_array)){
	foreach ($sectionlist_array  as $current_element){
		if(!strlen($current_element[section_title]))
			$current_element[section_title]="--";
		$moderation_list = $moderation_list.'<a href="section.php?section_id='.$current_element[section_id]."\">$current_element[section_title]</a><br>";
		}
		$t->set_var("moderated_links",$moderation_list."<br>");
		$t->set_var("moderated_title","Moderator of<br>");
	} else {
		$t->set_var("moderated_title","");
	}

	if($last_edit_array = get_last_edited_topic($examine_user_array[user_id])){
		$t->set_var("Most_Recent_Post_in","Most Recent Post In<br>");
		if(!strlen($last_edit_array[topic_title]))
			$last_edit_array[topic_title]="--";
		$t->set_var("topic_link",'<a href="readtopic.php?topic_id='.$last_edit_array[topic_id].'">'.$last_edit_array[topic_title].'</a>');
	} else {
		$t->set_var("Most_Recent_Post_in","");
		$t->set_var("topic_link","");
	
	}
}



$t->set_var("number_of_posts",$examine_user_array[user_totaledits]);


if($last_time_on = get_last_time_on($examine_user_array[user_id])){
	$t->set_var("last_visit",$last_time_on);
} else {
	$t->set_var("last_visit","Unknown");
}
$t->set_var("user_totalvisits",$examine_user_array[user_totalvisits]);
$t->set_var("user_film",$examine_user_array[user_film]);
$t->set_var("user_band",$examine_user_array[user_band]);
$t->set_var("user_sex",$examine_user_array[user_sex]);
$t->set_var("user_town",$examine_user_array[user_town]);
$t->set_var("user_age",$examine_user_array[user_age]);

$t->set_var("shown_user_id",$examine_user_array[user_id]);


#set sysop flag to no before calling get_sectionlist_array

#show user comments



if($comments_array =  get_user_comment($examine_user_array[user_id])){
	$t->set_block('userinfo', 'CommentBlock', 'tablerow'); 
	foreach ($comments_array as $current_message_array){
        $t->set_var("comment_from_id",$current_message_array[from_id]);
	    $t->set_var("comment_text",$current_message_array[text]); # strip this?
	    $t->set_var("comment_user",$current_message_array[user_name]);		  
	    $t->parse('tablerow', 'CommentBlock', true); 	
		
	}
}

$t->pparse("MyFinalOutput","userinfo");

page_end($breadcrumbs);
?>
