<?php

# this should all be replaced by templates and functions in interface_layer.php

// functions used to control formatting

// colours and things

$theme = "crystal";
//$theme = "classic";
if ($theme == "crystal") {
	define('COLOUR_PAGE_TITLE_BK','#d6dff5');
	
	define('COLOUR_HIGHLIGHT','#000000');
	define('COLOUR_MSG_BK','#FFFFFF');
	define('COLOUR_LINE','#99ff33');
	define('COLOUR_TOPIC_HEADER_BK','#d6dff5');
	define('COLOUR_TOPIC_HEADER_TXT','#99ff33');

	

	define('COLOUR_PAGE_TITLE_TXT','#000000');
	define('COLOUR_PAGE_LINK','#0000CC');
	define('COLOUR_PAGE_VLINK','#0000CC');
	define('COLOUR_PAGE_BK','#FFFFFF');
	define('COLOUR_PAGE_TXT','#000000');
	define('FONT_FACE','Verdana, Arial, Helvetica, sans-serif');
	define('SECTION_GRAPHIC',' <img 
src="images/xp/folder-normal.gif" align="left" alt="menu" border="0"> ');
	define('SECTION_GRAPHIC_MSG',' <img 
src="images/xp/folder-new.gif" align="left" alt="menu" border="0"> ');
	define('NEW_MESSAGE_IMAGE',' <a href="messages.php"><img src="images/xp/ani.gif"  border="0"  alt="new messages waiting"></a> ');
	define('SEND_MESSAGE_IMAGE','<img src="msg.gif" alt="[ reply ]" >');
} 

// end the colours

function htmlheader($pagetitle,$url,$check) {
session_start();
?>
<html>
<head>
<title><?php
if (isset($pagetitle)) {
   echo $pagetitle;
} else
{
echo "Message Board";
}

echo "</title>";

global $current_id;

if(session_is_registered("current_id")) {
#	echo "Debug status: never gonna work, give up, get drunk<br>";
	$user_id = $current_id;
} else {
	$user_id = 0;
}


#echo "debug - $user_id - $current_id - <br>";
if(($check=='1') and (!validlogin()))
{
   ?>
   <meta http-equiv="Refresh" content="1;url=index.php">
   </head><body></body></html>
   <?php
   exit();
  

} else {
  if(isset($url)){
  ?>
  <meta http-equiv="Refresh" content="1;url=<?php echo $url?>">
  <?php
  }
}

echo "<!-- $user_id -->\n\n";
if($user_id != 0) {
  $sql = "UPDATE usertable SET user_location=\"".$pagetitle."\" WHERE user_id=$user_id";
  $errortext = mysql_query($sql);
  echo "<!-- $sql \n $errortext -->";
}
?>

</head>
<body bgcolor=<?php
echo COLOUR_PAGE_BK." TEXT=".COLOUR_PAGE_TXT." LINK=".COLOUR_PAGE_LINK." VLINK=".COLOUR_PAGE_VLINK;
?>
><font face="<?php echo FONT_FACE ?>">
<div align="left">
<?php
} // end posthtmlheader



function htmlfooter(){
?>
</font>
</div>
</body>
</html>
<?php

} // end htmlfooter

function drawline(){
        echo "<hr noshade=\"1\" color=".COLOUR_LINE." size=\"1\">";
}

function topicheader()
// define look of topic headers
// change colour if there are messages
{
?>
<table bgcolor=<?php echo COLOUR_TOPIC_HEADER_BK ?> align="center"   border="0" width="100%"
cellpadding="4" cellspacing="0"><tr><td><font size="-1" face="<?php echo FONT_FACE ?>">
<?php
}

function topicfooter()
{
// counterpart to topicheader
?>
</font></td></tr></table>
<?php
}

function pagetitle($title) {
if(isset($title))
{
	$tmp_bk = COLOUR_PAGE_TITLE_BK;
	$tmp_fg = COLOUR_PAGE_TITLE_TXT;
    global $current_id;

	if(isset($current_id)){
	#if(get_count_unread_messages($_SESSION[current_id])>0)
		if(get_count_unread_messages($current_id)>0) {
	     # $tmp_fg = COLOUR_PAGE_TITLE_BK;
		 # $tmp_bk = COLOUR_PAGE_TITLE_TXT;
		 $title = NEW_MESSAGE_IMAGE.$title;

		}
	} 
	
	
	?>
	<table bgcolor=<?php echo $tmp_bk ?> align="center" border="0" width="100%" cellpadding="4" cellspacing="0">
	<tr>
	<td><font size="+2" color=<?php echo $tmp_fg ?> face="<?php echo FONT_FACE ?>">	<?php echo $title  ?>
	</font>
	</td>
	<td width="0">
	<div align="right"><font size="-2" color="<?php echo $tmp_fg ?>" face="Verdana, Arial, Helvetica, sans-serif">
	logout <a href="logout.php"><img src="images/xp/logout.gif"  border=0 align="middle"></a>
	</font></div></td>		
	</tr>
	</table>
	<br>
	<?php

} else {
// do nothing
} 

} // end pagetitle

// end theme functions
?>
