<?php 
// non-visual functions
// this will eventually be replaced by database_layer.php
// env
putenv("TZ=GB");
// needed libs
include('../phplib/php/template.inc');
include('database_layer.php');
include('interface_layer.php');
// defines
define('TEMPLATE_HOME', '../templates/');
define('DEFAULT_TEMPLATE', 'xp');
// you may wish to change the following values
define('SYSOP_NAME', "Christian");
define('SYSOP_MAIL', 'sysop@nexus5.org.uk');
// functions
function opendata()
{
	// quick function to open the database connection
	// update with your details
	$db = mysql_pconnect('localhost:/tmp/mysql.cable109', "sysop", "PASSWORD");
	// $db = mysql_pconnect('localhost',"root","");
	mysql_select_db("fraggle", $db);

	return $db;
} 

function validlogin()
{
	// to check to see if there is a valid current_id session var
	if (session_is_registered("current_id")) {
		// echo "logged in";
		// put user_id in to loggedin table
		// for a list of who's on
		// echo " $_id<br>";
		$db = opendata();
		global $current_id;

		$sql = "DELETE FROM whoison WHERE user_id=$current_id";
		mysql_query($sql, $db);
		$sql = "INSERT INTO whoison (user_id) VALUES ('$current_id')";
		mysql_query($sql, $db);

		return true;
	} else {
		// echo "Invalid Login";
		return false;
	} 
} 

function is_sysop($user_id)
{
	$sql = "SELECT user_sysop FROM usertable where user_id=$user_id";
	if ($user_id != null) {
		$sysopinfo = mysql_query($sql);
		$sysopresult = mysql_result($sysopinfo, 0, "user_sysop");
		if ($sysopresult == 'y') {
			return true;
		} else {
			return false;
		} 
	} else {
		return false;
	} 
} 

function is_message_owner($message_id, $user_id, $db)
{
	$sql = "SELECT sectiontable.user_id FROM messagetable, topictable,sectiontable
           WHERE message_id=$message_id AND
           messagetable.topic_id=topictable.topic_id AND
           sectiontable.section_id=topictable.section_id"; 
	// echo $sql;
	$ownerinfo = mysql_query($sql);
	$owner = mysql_result($ownerinfo, 0, "user_id");

	if (($owner == $user_id) or is_sysop($user_id)) {
		return true;
	} else {
		return false;
	} 
} 

function is_topic_owner($topic_id, $user_id, $db)
{
	$sql = "SELECT sectiontable.user_id FROM topictable,sectiontable
           WHERE topictable.topic_id=$topic_id AND
           sectiontable.section_id=topictable.section_id";

	$ownerinfo = mysql_query($sql);
	$owner = mysql_result($ownerinfo, 0, "user_id");

	if (($owner == $user_id) or is_sysop($user_id)) {
		return true;
	} else {
		return false;
	} 
} 

function is_section_owner($section_id, $user_id, $db)
{
	$sql = "SELECT user_id FROM sectiontable
           WHERE section_id=$section_id";

	$ownerinfo = mysql_query($sql);
	$owner = mysql_result($ownerinfo, 0, "user_id");

	if (($owner == $user_id) or is_sysop($user_id)) {
		return true;
	} else {
		return false;
	} 
} 

function get_section_owner($section_id, $db)
{
	$sql = "SELECT user_id FROM sectiontable WHERE section_id=$section_id";
	$ownerinfo = mysql_query($sql);
	$owner = mysql_result($ownerinfo, 0, "user_id");
	return $owner;
} 

function get_topic_name($topic_id, $db)
{
	$sql = "SELECT topic_title FROM topictable WHERE topic_id=$topic_id";
	$topicinfo = mysql_query($sql);
	$topic = mysql_result($topicinfo, 0, "topic_title");
	return $topic;
} 

function is_user_owner($currentuser, $user)
{
	
	if ($currentuser == $user) {
		return true;
	} else {
		return false;
	} 
} 

function is_message_secret($message_id)
{
	$sql = "SELECT topictable.topic_annon FROM topictable, messagetable
           WHERE messagetable.message_id=$message_id AND
           messagetable.topic_id=topictable.topic_id";

	$ownerinfo = mysql_query($sql);
	$owner = mysql_result($ownerinfo, 0, "topictable.topic_annon");

	if ($owner == 'n') {
		return false;
	} else {
		return true;
	} 
} 

function get_username($user_id)
{
	$sql = "SELECT user_name FROM usertable WHERE user_id=$user_id";
	$usernameinfo = mysql_query($sql);
	$userresult = mysql_result($usernameinfo, 0, "user_name");

	return $userresult;
} 

function newmessages($user_id)
{
	/**
	 * returns true if user has messages waiting to be read
	 * 
	 * last update Nov 3 2001 - xian
	 */
	$sql = "SELECT nexusmessage_id FROM nexusmessagetable WHERE user_id = $user_id";

	if (!$nexusmessageinfo = mysql_query($sql)) {
		nexus_error();
	} 

	if (mysql_num_rows($nexusmessageinfo)) {
		return true;
	} else {
		return false;
	} 
} 

function count_instant_messages($user_id)
{
	// this is intended to replace the user of newmessages()
	$sql = "SELECT nexusmessage_id FROM nexusmessagetable WHERE user_id=$user_id";

	if (!$nexusmessageinfo = mysql_query($sql)) {
		nexus_error();
	} 

	if ($num_msg = mysql_num_rows($nexusmessageinfo)) {
		return $num_msg;
	} else {
		return false;
	} 
} 

function count_messages_in_topic($topic_id)
{
	// returns the total amount of messages in a given topic
	// returns false if any probs
	$sql = "SELECT COUNT(message_id) AS total_msg FROM messagetable WHERE topic_id=$topic_id";

	if (!$message_info = mysql_query($sql)) {
		return false;
	} 

	if (mysql_num_rows($message_info)) {
		$message_array = mysql_fetch_array($message_info, MYSQL_ASSOC);
		return $message_array[total_msg];
	} else {
		return false;
	} 
} 
function displaymessage($messagerow, $topic_id, $db, $user)
{
	$userinfo = mysql_query("SELECT * FROM usertable WHERE user_id=$messagerow[user_id]", $db);
	$userrow = mysql_fetch_array($userinfo);

	$sql = "SELECT  user_id FROM topictable,sectiontable WHERE topictable.section_id=sectiontable.section_id and topictable.topic_id=$topic_id";

	$ownerinfo = mysql_query($sql);
	if ($user) {
		$ownerrow = mysql_fetch_array($ownerinfo);
		$owner = $ownerrow[0];
	} else {
		$owner = "invalid";
	} 
	// echo $sql;
	$sql = "SELECT section_id FROM topictable WHERE topic_id=$messagerow[topic_id]";
	$sectioninfo = mysql_query($sql, $db);
	$sectionrow = mysql_fetch_array($sectioninfo); 
	// current section owner is now in owerrow[1]
	$sql = "SELECT DATE_FORMAT(message_time,\"%a %b %D - %H:%i %Y\") FROM messagetable WHERE message_id=$messagerow[message_id]";
	$dateinfo = mysql_query($sql, $db);
	$daterow = mysql_fetch_array($dateinfo);

	topicheader();

	echo "<font color=" . COLOUR_HIGHLIGHT . ">";
	if (is_message_secret($messagerow["message_id"], $user, $db)) {
		if (is_message_owner($messagerow["message_id"], $user, $db)) {
			echo "<b>Secret: </b></font>(" . $messagerow["message_popname"];
			echo ") <a href=\"myinfo.php?section_id=$sectionrow[0]&lookat=$userrow[0]\">$userrow[1]</a>";
			echo " <br>";
		} else {
			echo "<b>From:</b></font> ( Popname Hidden ) Author Hidden";
			echo "<br>";
		} 
	} else {
		echo "<b>From: </b></font>(" . $messagerow["message_popname"];
		echo ") <a href=\"myinfo.php?section_id=$sectionrow[0]&lookat=$userrow[0]\">$userrow[1]</a>";
		echo " <br>";
	} 

	echo "<font color=" . COLOUR_HIGHLIGHT . ">";
	echo "<b>Date: </b></font>$daterow[0]<br>";

	echo "<font color=" . COLOUR_HIGHLIGHT . ">";
	if (strlen($messagerow["message_title"])) {
		echo "<b>Subject:</b> </font>" . $messagerow["message_title"];
	} else {
		echo "</font>";
	} 
	// echo "</font>";
	if (is_message_owner($messagerow["message_id"], $user, $db)) {
		echo "<a href=\"delpost.php?message_id=$messagerow[message_id]&topic_id=$topic_id\"><br>[ remove ]</a>";
		echo " -- <a href=\"alterpost.php?message_id=$messagerow[message_id]&topic_id=$topic_id\">[ edit ]</a>";
	} 
	topicfooter();

	?>
         <table bgcolor=<?php echo COLOUR_MSG_BK ?> align="center"  border="0" width="100%"
         cellpadding="4" cellspacing="0"><tr><td width="100%"><font face="<?php echo FONT_FACE ?>">
         <?php
	echo "<font face=\"" . FONT_FACE . "\">" . nx_code($messagerow["message_text"]) . "</font>";

	?>
          </font></td></tr></table>
          <?php

} 

function displaytopic($topicrow, $db, $user)
{ 
	// topicheader();
	echo "\n\n\n";
	echo '<table width="100%"><tr><td>';
	$new_msg = new_messages_in_topic($topicrow[0], $user);
	if ($new_msg > 0) {
		echo "<b><FONT size=+1>";
		echo "<a href=\"readtopic.php?section_id=$topicrow[2]&topic_id=$topicrow[0]\">";
		echo "<img src=\"images/xp/star.gif\" alt=\"*\" border=\"0\">$topicrow[1]<img src=\"images/xp/star.gif\" alt=\"*\" border=\"0\">";
		echo "</FONT></a></b>";
		echo "<blockquote>$topicrow[3]<br>"; 
		// echo "<br><br><font size=\"-1\" color=".COLOUR_HIGHLIGHT."><b>$new_msg</b> New Messages</font><br>";
	} else {
		echo "<a href=\"readtopic.php?section_id=$topicrow[2]&topic_id=$topicrow[0]\">";
		echo "<FONT  size=+1><b>$topicrow[1]</b></font></a>";
		echo "<blockquote>$topicrow[3]<br>";
	} 

	echo '<font size="-1">';
	if (is_topic_owner($topicrow[0], $user, $db)) {
		echo "<a href=\"altertopic.php?topic_id=$topicrow[0]\">[ edit ]</a>";
		echo" --- <a href=\"deltopic.php?topic_id=$topicrow[0]\">[ delete ]</a> -- ";
	} 
	// if( (strlen($topicrow[1]))  && ($new_msg!=-1) )
	// echo"<a href=\"unsub.php?section_id=$topicrow[2]&topic_id=$topicrow[0]\">[ unsubscribe ]</a>";
	if (!unsubscribed_from_topic($topicrow[0], $_SESSION[current_id]))
		echo"<a href=\"unsub.php?section_id=$topicrow[2]&topic_id=$topicrow[0]\">[ unsubscribe ]</a>";

	echo "</blockquote></font>";
	echo "\n</td></tr></table>";
} 

function sectionheader($myrow)
{
	global $current_id;
	$db = opendata();

	$user_array = get_user_array($current_id);

	echo "<h2><a href=\"section.php?section=" . $myrow["section_id"];

	if (new_messages_in_section($current_id, $myrow[section_id])) {
		echo "\">" . SECTION_GRAPHIC_MSG . $myrow["section_title"] . "</a></h2>";
	} else {
		echo "\">" . SECTION_GRAPHIC . $myrow["section_title"] . "</a></h2>";
	} 

	if ($num = get_count_section_messages($myrow[section_id])) {
		$sql = "SELECT messagetable.message_id FROM messagetable, topictable, topicview WHERE messagetable.topic_id = topictable.topic_id AND topictable.section_id =" . $myrow["section_id"] . " AND topicview.user_id =$current_id AND messagetable.message_time > topicview.msg_date AND topicview.topic_id = messagetable.topic_id";
		echo "<font size=-2>" . $num . " messages</font>";
	} 
	// add section owner functions references here
	if (can_user_edit_section($user_array, $myrow)) { // # ADD moderator here too!
		echo '<font size="-1"><a href="altersection.php?section_id=' . $myrow[section_id] . '">[ edit ]<a></font><br>';
	} 
	echo "<br>";
} 

function nexus_error()
{
	/**
	 * displays a meaninful error message
	 * 
	 * update nov 17 2001 - added session_start
	 * update nov 2nd 2001 - xian
	 */

	/**
	 * $template_location = TEMPLATE_HOME.DEFAULT_TEMPLATE; 
	 * 
	 * 
	 * 
	 * $t = new Template($template_location);
	 * $t->set_file("MyFileHandle", "page.html");
	 * 
	 * $t->set_var("pagetitle","Error");
	 * $error_message =  "<br>Sorry, something has gone all wrong. Life is a bit like that sometimes.<br>";
	 * $error_message .= "Please make a note of the message below and tell <a href=\"mailto:".SYSOP_MAIL."\">";
	 * $error_message .= SYSOP_NAME."</a><br><br>";
	 * $error_message .= "ERROR: ".mysql_errno()." ".mysql_error();
	 * $error_message .= '<br><br><a href="index.php">Restart Nexus</a>';
	 * $t->set_var("content",$error_message);
	 * session_start();
	 * session_destroy();
	 * $t->parse("MyOutput","MyFileHandle");
	 * $t->p("MyOutput");
	 * exit();
	 */
	eject_user();
} 

function update_location($location)
{
	/**
	 * updates user table with location of user passed via location arg
	 * 
	 * update nov 3 2001 - xian
	 */
	global $current_id;

	$sql = "UPDATE usertable SET user_location=\"" . $location . "\" WHERE user_id=$current_id";
	if (!$sql_result = mysql_query($sql)) {
		// echo $sql;
		nexus_error();
	} 

	update_last_time_on($current_id);
} 

function new_messages_in_topic($topic_id, $user_id)
{
	/**
	 * takes a user_id and topic_id and returns the number of unread messages
	 * else false is returned
	 */ 
	// check to see if unsubbed
	if (!unsubscribed_from_topic($topic_id, $user_id)) {
		// ###
		$sql = "SELECT msg_date FROM topicview WHERE user_id=$user_id AND topic_id=$topic_id LIMIT 1";

		if (!$last_message_result = mysql_query($sql)) {
			nexus_error();
		} 

		if (mysql_num_rows($last_message_result)) {
			$last_message = mysql_fetch_array($last_message_result);

			$sql = "SELECT COUNT(messagetable.message_id) AS new_msg_count FROM messagetable  WHERE topic_id=$topic_id AND message_time > " . $last_message["msg_date"];

			if (!$new_message_result = mysql_query($sql)) {
				nexus_error();
			} 
			$new_messages_array = mysql_fetch_array($new_message_result);
			$new_messages = $new_messages_array[new_msg_count];
		} else { // never read, check to see if there are any messages
			if (count_messages_in_topic($topic_id)) {
				$new_messages = true;
			} else {
				$new_messages = false;
			} 
		} 
		// ##
	} else {
		$new_messages = false;
	} 

	return $new_messages;
} 

function get_score($user_id)
{
	$sql = "SELECT user_totalvisits, user_totaledits FROM usertable WHERE user_id=$user_id";
	if (!$user_results = mysql_query($sql)) {
		nexus_error();
	} 
	$user_row = mysql_fetch_array($user_results);
	if ($user_row["user_totalvisits"]) {
		$user_score = round($user_row["user_totaledits"] / $user_row["user_totalvisits"] + $user_row["user_totaledits"] / 1000);
	} else {
		$user_score = 0;
	} 
	// now find out the score from being an owner
	// er total edits in sections div 2000 I think should be fair
	$sql = "SELECT count(messagetable.message_id) FROM messagetable, topictable, sectiontable WHERE messagetable.topic_id = topictable.topic_id AND topictable.section_id = sectiontable.section_id and sectiontable.user_id=$user_id";

	if (!$section_results = mysql_query($sql)) {
		nexus_error();
	} 
	// $section_row = mysql_fetch_array($section_results);
	$section_score = mysql_result($section_results, 0);

	$score = $user_score + round($section_score / 2000);

	return $score;
} 

function nx_code($text)
{
	/**
	 * replaces nx code with html for
	 * links and images 
	 * 
	 * vaguely based on bb code
	 * 
	 * [web-] = <a href="
	 * [-web] = " target="_blank">[ WWW link ]</a>
	 * 
	 * [picture-] = <img src="
	 * [-picture] = ">
	 */

	$text = str_replace('[www-]', '[WWW-]', $text);
	$text = str_replace('[-www]', '[-WWW]', $text);
	$text = str_replace('[picture-]', '[PICTURE-]', $text);
	$text = str_replace('[-picture]', '[-PICTURE]', $text);
	$text = str_replace('[-ascii]', '[-ASCII]', $text);
	$text = str_replace('[ascii-]', '[ASCII-]', $text);
	
	$first_www_dashpos = strpos($text, '[WWW-]');

	if (!is_integer(!$first_www_dashpos)) {
		if (substr_count($text, '[WWW-]') == substr_count($text, '[-WWW]')) {
			$text = str_replace('[WWW-]', '<a href="', $text);
			$text = str_replace('[-WWW]', '" target="_blank">[ WWW link ]</a>', $text);
		} 
	} 

	$first_pic_dashpos = strpos($text, '[PICTURE-]');
	print "<!-- starts at  $first_pic_dashpos -->";

	if (!is_integer(!$first_pic_dashpos)) {
		print "<!-- gets here -->";
		if (substr_count($text, '[PICTURE-]') == substr_count($text, '[-PICTURE]')) {
			$text = str_replace('[PICTURE-]', '<img src="', $text);
			$text = str_replace('[-PICTURE]', '">', $text);
		} 
	} 
	// ASCII
	$first_ascii_dashpos = strpos($text, '[PICTURE-]');
	print "<!-- starts at  $first_ascii_dashpos -->";

	if (!is_integer(!$first_ascii_dashpos)) {
		print "<!-- gets here -->";
		if (substr_count($text, '[ASCII-]') == substr_count($text, '[-ASCII]')) {
			$text = str_replace('[ASCII-]', '<tt>', $text);
			$text = str_replace('[-ASCII]', '</tt>', $text);
		} 
	} 

	return $text;
} 

function emotetext($text)
{
	/**
	 * takes text and replaces common text emotes with htmled graphical emotes
	 * intention is that things will call this up before displaying messages etc
	 * so the database is untouched 
	 * 
	 * 
	 * Christian - July 2002
	 * 
	 * - July 25 - put spaces round emotes so they pick up less unintentioned emotes - cfc
	 * emotes supported
	 * 
	 * happy :) :-) : )
	 * unhappy :( : ( :-(  
	 * angry :-@ :@
	 * confused :-S :S :s
	 * crying :'(
	 * super happy :-D : D :d : d
	 * tounge smile :-P :P ; P :p
	 * what :-| : | :|
	 * wink ;-) ;) ; )
	 * 
	 * 
	 * emotes ripped off msn until we come up with a nicer set 
	 * 
	 * to do
	 * come up with no ms replacement emotes
	 * - was going to use hudsons but now he's in a sulk, why did i ever bother?
	 * 
	 * give uses ability to not bw shown emotes 
	 * find a better way to do this, I think this might not be the fastest way
	 */
	$emotedtext = $text;
	// sod this
	return $text;
	// happy
	$emotedtext = str_replace(' :) ', ' <img src="emotes/regular_smile.gif" alt="* happy *"> ', $emotedtext);
	$emotedtext = str_replace(' : ) ', ' <img src="emotes/regular_smile.gif" alt="* happy *"> ', $emotedtext);
	$emotedtext = str_replace(' :-) ', ' <img src="emotes/regular_smile.gif" alt="* happy *"> ', $emotedtext); 
	// unhappy
	$emotedtext = str_replace(' :( ', ' <img src="emotes/sad_smile.gif" alt="* unhappy *"> ', $emotedtext);
	$emotedtext = str_replace(' :-( ', ' <img src="emotes/sad_smile.gif" alt="* unhappy *"> ', $emotedtext);
	$emotedtext = str_replace(' : ( ', ' <img src="emotes/sad_smile.gif" alt="* unhappy *"> ', $emotedtext); 
	// angry
	$emotedtext = str_replace(' :@ ', ' <img src="emotes/angry_smile.gif" alt="* angry *"> ', $emotedtext);
	$emotedtext = str_replace(' : @ ', ' <img src="emotes/angry_smile.gif" alt="* angry *"> ', $emotedtext);
	$emotedtext = str_replace(' :-@ ', ' <img src="emotes/angry_smile.gif" alt="* angry *"> ', $emotedtext); 
	// confused
	$emotedtext = str_replace(' :S ', ' <img src="emotes/confused_smile.gif" alt="* confused *"> ', $emotedtext);
	$emotedtext = str_replace(' :-S ', ' <img src="emotes/confused_smile.gif" alt="* confused *"> ', $emotedtext);
	$emotedtext = str_replace(' :s ', ' <img src="emotes/confused_smile.gif" alt="* confused *"> ', $emotedtext);
	$emotedtext = str_replace(' :S ', ' <img src="emotes/confused_smile.gif" alt="* confused *"> ', $emotedtext);
	$emotedtext = str_replace(': S', ' <img src="emotes/confused_smile.gif" alt="* confused *"> ', $emotedtext);
	$emotedtext = str_replace(' :-s ', ' <img src="emotes/confused_smile.gif" alt="* confused *"> ', $emotedtext); 
	// crying
	$emotedtext = str_replace(" :&#039;( ", ' <img src="emotes/cry_smile.gif" alt="* crying* "> ', $emotedtext); 
	// super happy
	$emotedtext = str_replace(' :-D ', ' <img src="emotes/teeth_smile.gif" alt="* super happy *"> ', $emotedtext);
	$emotedtext = str_replace(' : D ', ' <img src="emotes/teeth_smile.gif" alt="* super happy *"> ', $emotedtext);
	$emotedtext = str_replace(' :-d ', ' <img src="emotes/teeth_smile.gif" alt="* super happy *"> ', $emotedtext);
	$emotedtext = str_replace(' : d ', ' <img src="emotes/teeth_smile.gif" alt="* super happy *"> ', $emotedtext);
	$emotedtext = str_replace(' :D ', ' <img src="emotes/teeth_smile.gif" alt="* super happy *"> ', $emotedtext);
	$emotedtext = str_replace(' :d ', ' <img src="emotes/teeth_smile.gif" alt="* super happy *"> ', $emotedtext); 
	// rasp
	$emotedtext = str_replace(' :-P ', ' <img src="emotes/tounge_smile.gif" alt="* pulling a face *"> ', $emotedtext);
	$emotedtext = str_replace(' : P ', ' <img src="emotes/tounge_smile.gif" alt="* pulling a face *"> ', $emotedtext);
	$emotedtext = str_replace(' :P ', ' <img src="emotes/tounge_smile.gif" alt="* pulling a face *"> ', $emotedtext);
	$emotedtext = str_replace(' :-p ', ' <img src="emotes/tounge_smile.gif" alt="* pulling a face *"> ', $emotedtext);
	$emotedtext = str_replace(' : p ', ' <img src="emotes/tounge_smile.gif" alt="* pulling a face *"> ', $emotedtext);
	$emotedtext = str_replace(' :p ', ' <img src="emotes/tounge_smile.gif" alt="* pulling a face *"> ', $emotedtext); 
	// what?
	$emotedtext = str_replace(' :| ', ' <img src="emotes/whatchutalkingabout_.gif" alt="* frown *"> ', $emotedtext);
	$emotedtext = str_replace(' : | ', ' <img src="emotes/whatchutalkingabout_.gif" alt="* frown *"> ', $emotedtext);
	$emotedtext = str_replace(' :-| ', ' <img src="emotes/whatchutalkingabout_.gif" alt="* frown *"> ', $emotedtext); 
	// wink
	$emotedtext = str_replace(' ;) ', ' <img src="emotes/wink_smile.gif" alt="* cheeky *"> ', $emotedtext);
	$emotedtext = str_replace(' ; ) ', ' <img src="emotes/wink_smile.gif" alt="* cheeky *"> ', $emotedtext);
	$emotedtext = str_replace(' ;-) ', ' <img src="emotes/wink_smile.gif" alt="* cheeky *"> ', $emotedtext);
	return $emotedtext;
} 

function get_section_parent_info($section_id)
{ 
	// returns info about the parent section if any
	$sql = "SELECT section_id, section_title, parent_id FROM sectiontable WHERE section_id = $section_id";

	if (!$section_info = mysql_query($sql)) {
		return false;
	} 

	if (mysql_num_rows($section_info)) {
		$section_result = mysql_fetch_array($section_info, MYSQL_ASSOC);
		return $section_result;
	} else {
		return false;
	} 
} 

function get_user_array($user_id)
{
	/**
	 * takes user_id and returns an array of their userinfo
	 * INPUT user_id
	 * OUTPUT assoc array of user_info or false if user is not found
	 */
	// get array
	$sql = "SELECT * FROM usertable WHERE user_id = $user_id";

	if (!$user_info = mysql_query($sql)) {
		return false;
	} 

	if (mysql_num_rows($user_info)) {
		$user_array = mysql_fetch_array($user_info, MYSQL_ASSOC);
		return $user_array;
	} else {
		return false;
	} 
} 

function get_section_parents($section_id)
{
	/**
	 * IMPUT topic_id
	 * OUTPUT assoc array of topic_id and topic_title of all parent topics
	 */

	$count = 0;
	$current_breadcrumb = get_section_parent_info($section_id);
	$breadcrumb_array[$count]["section_id"] = $current_breadcrumb["section_id"];
	$breadcrumb_array[$count]["section_title"] = $current_breadcrumb["section_title"];
	$count++; 
	// echo "then onto topic ".$current_crumb["topic_topic_id"]."<br>";
	while ($current_breadcrumb = get_section_parent_info($current_breadcrumb["parent_id"])) {
		$breadcrumb_array[$count]["section_id"] = $current_breadcrumb["section_id"];
		$breadcrumb_array[$count]["section_title"] = $current_breadcrumb["section_title"];
		$count++;
	} 

	if ($count) {
		return array_reverse($breadcrumb_array);
	} else {
		return false;
	} 
} 

function get_breadcrumbs($section)
{
	// update this when newsection is real
	$breadcrumbs = get_section_parents($section);
	$num_of_crumbs = count($breadcrumbs);

	for($loop_count = 0; $loop_count < $num_of_crumbs-1; $loop_count++) {
		$crumb_urls .= '<a href="section.php?section_id=' . $breadcrumbs[$loop_count]["section_id"] . '">';
		if(strlen($breadcrumbs[$loop_count]["section_title"]))
			$crumb_urls .= $breadcrumbs[$loop_count]["section_title"] . "</a> -&gt; ";
		else
			$crumb_urls .= " - " . "</a> -&gt; ";
	} 
	return $crumb_urls;
} 

function get_breadcrumbs_topic($section)
{
	// update this when newsection is real
	$breadcrumbs = get_section_parents($section);
	$num_of_crumbs = count($breadcrumbs);

	for($loop_count = 0; $loop_count < $num_of_crumbs; $loop_count++) {
		$crumb_urls .= '<a href="section.php?section_id=' . $breadcrumbs[$loop_count]["section_id"] . '">';
		if(strlen($breadcrumbs[$loop_count]["section_title"]))
			$crumb_urls .= $breadcrumbs[$loop_count]["section_title"] . "</a> -&gt; ";
		else
			$crumb_urls .= " - " . "</a> -&gt; ";
	} 
	return $crumb_urls;
} 

?>
