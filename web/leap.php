<?php
/* 

leaps to the next section containg unread messages or to main menu

update log
2 Nov 2001 - updated function to use templates - xian

todo:
do not leap to unsubbed files
do not leap to files we do not have the privs to read
do not leap to files in sections we are banned from
*/

//includes
include('../includes/theme.php');
include('../includes/database.php');


//common stuff
$db = opendata();
session_start();
#$template_location =TEMPLATE_HOME.$my_theme; 

// check login
if (!validlogin()){
	eject_user();	
}

#get all section_ids, loop tho them all, break when new messages found and leap to seaction

$sql = "SELECT section_id  FROM sectiontable";


if(!$unreadsection = mysql_query($sql)){
	nexus_error();
}


if($current_section = mysql_fetch_array($unreadsection)){
 do {

		if(new_messages_in_section($_SESSION[current_id],$current_section[section_id])){
		// leap to section
		// exit
		
		header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section=$current_section[section_id]");
		echo '<font face="Verdana, Arial, Helvetica, sans-serif">Leaping to unread messages...</font>';
		exit;
		}
 	  
    } while ($current_section=mysql_fetch_array($unreadsection));

} else {
	// main menu
}

// leap to main menu
header("Location: http://".$_SERVER['HTTP_HOST']."/section.php?section=1");
echo '<font face="Verdana, Arial, Helvetica, sans-serif">Leaping to Main Menu</font>';