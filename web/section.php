<?php
include('../includes/theme.php');
include('../includes/database.php');

$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$my_theme; 

// check login
if (!validlogin()){
	eject_user();	
}

# ADD CHECKS HERE
$user_array = get_user_array($_SESSION[current_id]);

# temp - transsistional vars
if(isset($section_id))
 $section = $section_id;

# end temp

if(!$sectioninfo = mysql_query("SELECT section_id, section_title, user_id, parent_id FROM sectiontable WHERE section_id=$section",$db)){
 nexus_error();
}


if (!mysql_num_rows($sectioninfo)){
	# if the section does not exist then jump to main menu
	# update this to jump to section zero I guess
	// UPDATE THIS
	htmlheader($sectionname,"section.php?section=1",1);
	pagetitle("No such section...");
	exit();
}

if(!$sectioninfodetails = mysql_fetch_array($sectioninfo)){
	nexus_error();
} 


$sectionname=$sectioninfodetails["section_title"];
$sectionowner=$sectioninfodetails["user_id"];
$sectionparent=$sectioninfodetails["parent_id"];

$breadcrumbs=get_breadcrumbs($section);
 
update_location($sectionname);

if(!$ownernameinfo = mysql_query("SELECT user_name FROM usertable where user_id=$sectionowner")){
	nexus_error();
}

if(mysql_num_rows($ownernameinfo)){
	$ownername = mysql_result($ownernameinfo,0,"user_name");
} else {
	$ownername = "unknown moderator";
}

 

$t = new Template($template_location);
#replace this with template
#htmlheader($sectionname,NULL,1);
#pagetitle($sectionname);

# BEGIN DISPLAY TOPIC TITLE

// change page template if new messages are waiting


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
$t->set_var("pagetitle",$sectionname);
$t->set_var("breadcrumbs",$breadcrumbs);

$t->set_var("section_id",$section);


$t->set_var("owner_id",$sectionowner);
$t->set_var("ownername",$ownername);

$t->set_var("user_name",$user_array["user_name"]);
$t->set_var("user_popname",$user_array["user_popname"]);
$t->set_var("user_id",$_SESSION[current_id]);

$t->pparse("MyFinalOutput","WholePage");
# END DISPLAY TOPIC TITLE







### DO LINKS TEMPLATE


$topicinfo = mysql_query("SELECT * FROM topictable  WHERE topictable.section_id=$section ORDER BY topic_weight",$db);
### END LINKS TEMPLATE


# BEGIN DISPLAY TOP SET OF BUTTONS
if (is_section_owner($sectioninfodetails[section_id],$user_array[user_id],$db)){   
	$t->set_file('top_links','menu_topic_links_admin.html');
} else {
	$t->set_file('top_links','menu_topic_links.html');
}

#$t->set_block('top_links','PARENT_LINKS','');
$t->set_var("section_id",$section);


$t->pparse('content','top_links');

# END DISPLAY TOP SET OF BUTTONS
#echo "DEBUG session user id is ".$_SESSION[current_id]."<br>";
### topics

if($myrow = mysql_fetch_array($topicinfo)){
 do {

      displaytopic($myrow, $db,$current_id);
	  
	  ##debug
	  
	  //echo "<b>DEBUG unsubbed is ".unsubscribed_from_topic($myrow[topic_id],$_SESSION[current_id])." </b>";
	  ##end debug
	  

    } while ($myrow=mysql_fetch_array($topicinfo));
}else{

}


########### subsections


$sql = "SELECT * FROM sectiontable where parent_id = $section ORDER BY section_weight";

$sectioninfo = mysql_query($sql, $db);
   
$sectioninfo = mysql_query($sql, $db);          
	if(mysql_num_rows($sectioninfo) ) 
	{
		# table begin
		$count = 0;
		?>
		<TABLE width="100%" border="0">
		<?php
		if ($myrow = mysql_fetch_array($sectioninfo))
	        do {
	          if($count % 2 == 0 ) 
				  {
					echo "<TR VALIGN=TOP>";
					}
			
				echo "<TD>";
				sectionheader($myrow);

				
				#}
				
				
				echo "</b></font></TD>";
				$count ++;
				
	          } while ($myrow = mysql_fetch_array($sectioninfo));
			 
			  if($count % 2){
			   echo "<TD></TD>";
			  }
			  # table end
			  # check if we need a filler cell
			  ?>
			  </TABLE>
			  <?php
	   } else {
       #printf("<br><b> no sections yet ! </b><br>");
	   }


## DO LINKS TEMPLATE

# BEGIN DISPLAY TOP SET OF BUTTONS
if (is_section_owner($sectioninfodetails[section_id],$user_array[user_id],$db)){   
	$t->set_file('bottom_links','menu_menu_links_admin.html');
} else {
	$t->set_file('bottom_links','menu_topic_links.html');
}
$t->set_var("SECTION_ID",$section);
$t->pparse('content','bottom_links');


page_end($breadcrumbs);
#echo '<font size="-1">'.$breadcrumbs.'</font>';
#htmlfooter();


?>
