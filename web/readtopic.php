<?php

/* 
displays a number of posts in a given topic

update log
26 jan 2002 - updated function to use templates - xian

*/

// includes
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

// get info
if(!$user_array = get_user_array($_SESSION[current_id])){
	nexus_error();
}

if(!$owner_array = get_topic_owner($topic_id)){
	nexus_error();
}

if (!$topic_array=get_topic($topic_id)){
	nexus_error();
}

$breadcrumbs = get_breadcrumbs_topic($topic_array[section_id]);

//update user activity
update_location("reading ".$topic_array[topic_title]);

$total_messages = get_count_topic_messages($topic_array[topic_id]);

// how many messages to display

$new_msg_total = new_messages_in_topic($topic_array[topic_id],$user_array[user_id]);


$sql = 'SELECT *, DATE_FORMAT(message_time,"%a %b %D - %H:%i %Y") AS format_time FROM messagetable WHERE topic_id='.$topic_id.'  ORDER BY  message_id ';
$limit_sql = "";
if($user_array[user_display]=="255"){
// viewing all messages
	if($user_array[user_backwards]=="n"){
		$limit_sql = " ";
	} else {
		$limit_sql = " DESC ";
	}
	
} else {


	if( ($new_msg_total +2) < ($user_array[user_display]) ) {
		
		$num_of_messages_to_display = $user_array[user_display];
		
		
	} else {
		
		$num_of_messages_to_display = $new_msg_total + 2;
		
	// view depth		
	
	}
	
	# look at this later okay
	
	$limit_start = $total_messages - $num_of_messages_to_display;
	if($limit_start < 0)
		$limit_start=0;
		
	if($user_array[user_backwards]=="n"){
		$limit_sql = " LIMIT $limit_start, $num_of_messages_to_display";
	} else {
		$limit_sql = " DESC LIMIT $num_of_messages_to_display";
	}
}


$sql = $sql.$limit_sql;

echo "<!-- displaying $num_of_messages_to_display<br> $sql<br> -->";


// if backwards




// select messages to display


if(!$messages_to_show = mysql_query($sql, $db)){
	nexus_error();
}

// choose what template
$t = new Template($template_location);

// chose display mode

if($topic_array["topic_annon"]=='y') {
	if(is_topic_owner($topic_id, $current_id, $db)) {
		// can see
		//echo "DEBUG: annon and owner<br>";
		$t->set_file('topic_handle','secret_owner.html');
	} else {
		// can not see
		//echo "DEBUG: annon<br>";
		$t->set_file('topic_handle','secret_comment.html');
	}

} else {
	if(is_topic_owner($topic_id, $current_id, $db)) {
		// owner
		//echo "DEBUG: owner<br>";
		$t->set_file('topic_handle','normal_owner.html');
	} else {
		// not owner
		//echo "DEBUG: normal<br>";
		$t->set_file('topic_handle','normal_comment.html');
	}

}



//Topic Title

# BEGIN DISPLAY TOPIC TITLE

if(get_count_unread_messages($_SESSION[current_id])>0){
       $t->set_file("WholePage","mail_page.html");
} else {
        $t->set_file("WholePage","page.html");
}




if ($num_msg = count_instant_messages($_SESSION[current_id])){
	$t->set_var("num_msg",$num_msg);
}else{
	$t->set_var("num_msg","no");
}

##
$t->set_var("pagetitle",$topic_array[topic_title]);

$t->set_var("breadcrumbs",$breadcrumbs);

$t->set_var("owner_id",$owner_array[owner_id]);
$t->set_var("ownername",$owner_array[owner_name]);

$t->set_var("user_name",$user_array["user_name"]);
$t->set_var("user_popname",$user_array["user_popname"]);
$t->set_var("user_id",$_SESSION[current_id]);

$t->set_var("section_id",$topic_array[section_id]);

##


$t->pparse("MyFinalOutput","WholePage");

if( ($owner_array[owner_id] == $_SESSION[current_id]) or (is_sysop($_SESSION[current_id])) ) {
	$t->set_file('buttons','readtopic_links.html');
} else {
	if($topic_array["topic_readonly"]=='y'){
		$t->set_file('buttons','readtopic_links_readonly.html');
	} else {
		$t->set_file('buttons','readtopic_links.html');
	}
}
$t->set_var("section_id",$topic_array[section_id]);
$t->set_var("topic_id",$topic_id);
$t->pparse('content','buttons');

# END DISPLAY TOP SET OF BUTTONS

###DEBUG


#echo "DEBUG $owner_array[owner_name] and $owner_array[owner_id]";
###END DEBUG

if(mysql_num_rows($messages_to_show)){
	
	if(!$current_message = mysql_fetch_array($messages_to_show)){
		nexus_error();
	}

	$t->set_block('topic_handle','CommentBlock','messagerow');

	do {
			set_time_limit(60);			 
     	
			// get user message author info
			$sql = "SELECT user_name FROM usertable WHERE user_id=".$current_message["user_id"];
			if(!$message_user=mysql_query($sql,$db)){
				nexus_error();
			}
			$message_user_info=mysql_fetch_array($message_user);
			
			$t->set_var("username",$message_user_info["user_name"]);

			$t->set_var("section_id", $topic_array[section_id]);

			$t->set_var("user_moto",$current_message["message_popname"]);
            
			//replace emotes with html gubbings 
			//$t->set_var("edit",$current_message["message_text"]);

			$nx_message = nx_code($current_message["message_text"]);
			$t->set_var("edit",$nx_message);
			
			$t->set_var("user_id",$current_message["user_id"]);
			$t->set_var("date",$current_message["format_time"]);

			$t->set_var("message_id",$current_message["message_id"]);
			$t->set_var("topic_id",$topic_id);
			if(strlen($current_message["message_title"])){
				$t->set_var("subject","<b>Subject:</b> ".$current_message["message_title"]);
			}else{
				$t->set_var("subject","");
			}
            
            $t->pparse('messagerow','CommentBlock',false);

	} while ($current_message = mysql_fetch_array($messages_to_show));
	



	// now update last view time here
	
	subscribe_to_topic($topic_id, $_SESSION[current_id]);
	
	$sql = "DELETE FROM topicview WHERE topic_id=$topic_id AND user_id=$_SESSION[current_id]";
	if(!mysql_query($sql,$db)){
		nexus_error();
	}

	$sql = "SELECT max(message_time) FROM messagetable WHERE topic_id=$topic_id";
	if(!$timeinfo = mysql_query($sql, $db)) {
		nexus_error();
	}

	if (mysql_num_rows($timeinfo)){
		$lastdate = mysql_fetch_row($timeinfo);
		$sql = "INSERT INTO topicview (user_id, topic_id, msg_date) VALUES ('$current_id','$topic_id','$lastdate[0]')";  
		#UPDATE use add_topicview HERE
		if(!mysql_query($sql, $db)){
			nexus_error();
		}
	}
    
	//end update time

} else {
	// No messages to display 
}
 
# DISPLAY BOTTOM SET OF BUTTONS
if( ($owner_array[owner_id] == $_SESSION[current_id]) or (is_sysop($_SESSION[current_id])) ) {
	$t->set_file('buttons','readtopic_links.html');
} else {
	if($topic_array["topic_readonly"]=='y'){
		$t->set_file('buttons','readtopic_links_readonly.html');
	} else {
		$t->set_file('buttons','readtopic_links.html');
	}
}
$t->set_var("section_id",$topic_array[section_id]);
$t->set_var("topic_id",$topic_id);
$t->pparse('content','buttons');
# END DISPLAY BOTTOM SET OF BUTTONS
page_end($breadcrumbs);
#echo '<font face="Verdana, Arial, Helvetica, sans-serif" size="-1">'.$breadcrumbs.'</font>';

?>


























