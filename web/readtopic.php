<?php

/* 
displays a number of posts in a given topic, paginates them accounting to user preferences

*/
// includes
include('../includes/theme.php');
include('../includes/database.php');
// common stuff
$db = opendata();
session_start();
global $_SESSION;

$template_location = TEMPLATE_HOME . $my_theme; 
// check login
if (!validlogin()) {
    eject_user();
} 
// get info
if (!$user_array = get_user_array($_SESSION[current_id])) {
    nexus_error();
} 

if (!$owner_array = get_topic_owner($topic_id)) {
    nexus_error();
} 

if (!$topic_array = get_topic($topic_id)) {
    nexus_error();
} 

$breadcrumbs = get_breadcrumbs_topic($topic_array[section_id]);

$new_msg_total = new_messages_in_topic($topic_array[topic_id], $user_array[user_id]);
// update user activity
$location_str = '<a href="readtopic.php?section_id=' . $topic_array[section_id] . '&topic_id=' . $topic_array[topic_id] . '"><i>Reading</i> ' . $topic_array[topic_title] . '</a>';
update_location($location_str);
// update_location($sausages);
$total_messages = get_count_topic_messages($topic_array[topic_id]);
// how many messages to display
// should I be setting the limit here???
// if ($limit) {
// $limit = $new_msg_total = new_messages_in_topic($topic_array[topic_id],$user_array[user_id]);
// }
// ### new here ###
$sql = 'SELECT *, DATE_FORMAT(message_time,"%a %b %D - %H:%i %Y") AS format_time FROM messagetable WHERE topic_id=' . $topic_id . '  ORDER BY  message_id  ';

$limit_sql = "";

if (!isset($limit)) {
    // $limit=0; # this should perhaps be count messages minus user_displa
    $limit = $total_messages - $user_array[user_display];
    // $limit=$total_messages - $page_length;
    if ($limit < 0) {
        $limit = 0;
    } 
} else {
    if ($limit < 0) {
        $limit = 0;
    } 
} 

if ($new_msg_total <= $user_array[user_display]) {
    $page_length = $user_array[user_display];
} else {
    $page_length = $new_msg_total;
    $limit = $total_messages - $page_length;
} 
// $limit_sql = " LIMIT $limit , $user_array[user_display]";
$limit_sql = " LIMIT $limit , $page_length";

$sql = $sql . $limit_sql;
echo "\n\n<!-- DEBUG: $sql<br> -->";
echo "\n<!-- DEBUG: $location_str<br> -->";
echo "\n<!-- new messages $new_msg_total -->\n";
echo "<!-- page length $page_length -->\n";
echo "<!-- topic_array[topic_id] is  $topic_array[topic_id] -->\n";
echo "<!-- user_array[user_id] is $user_array[user_id] -->\n"; 
// ## end new here ###
// select messages to display
if (!$messages_to_show = mysql_query($sql, $db)) {
    nexus_error();
    // echo "$sql"; 
    // exit();
}
$messages_shown_count = mysql_num_rows($messages_to_show);
// choose what template
$t = new Template($template_location);
// chose display mode
if ($topic_array["topic_annon"] == 'y') {
    if (is_topic_owner($topic_id, $current_id, $db)) {
        // can see
        // echo "DEBUG: annon and owner<br>";
        $t->set_file('topic_handle', 'secret_owner.html');
    } else {
        // can not see
        // echo "DEBUG: annon<br>";
        $t->set_file('topic_handle', 'secret_comment.html');
    } 
} else {
    if (is_topic_owner($topic_id, $current_id, $db)) {
        // owner
        // echo "DEBUG: owner<br>";
        $t->set_file('topic_handle', 'normal_owner.html');
    } else {
        // not owner
        // echo "DEBUG: normal<br>";
        $t->set_file('topic_handle', 'normal_comment.html');
    } 
} 
// Topic Title
// BEGIN DISPLAY TOPIC TITLE


display_header($t,
	       $breadcrumbs,
	       $topic_array[topic_title],
	       $user_array["user_name"],
	       $user_array["user_popname"],
	       $_SESSION[current_id],
	       count_instant_messages($_SESSION[current_id]),
	       $owner_array[owner_id],
	       $owner_array[owner_name],
	       get_count_unread_comments($_SESSION[current_id]),
	       get_count_unread_messages($_SESSION[current_id]));

// page_header


#$t->set_var("section_id", $topic_array[section_id]);



// end display of topic title

if (($owner_array[owner_id] == $_SESSION[current_id]) or (is_sysop($_SESSION[current_id]))) {
    $t->set_file('buttons', 'readtopic_links_admin_start.html'); 
    // SELECT
    $select_code = '<option value="' . $topic_array[topic_id] . '">' . $topic_array[topic_title] . '</option>';
    $sectionlist_array = get_topiclist_array($user_array);
    foreach ($sectionlist_array as $current_element) {
        $select_code = $select_code . '<option value="' . $current_element[topic_id] . '">' . $current_element[topic_title] . '</option>';
    } 

    $t->set_var("SELECT_CODE", $select_code);

    $t->set_var("messages_shown", $messages_shown_count);
} else {
    if ($topic_array["topic_readonly"] == 'y') {
        $t->set_file('buttons', 'readtopic_links_readonly.html');
    } else {
        $t->set_file('buttons', 'readtopic_links.html');
    } 
} 
$t->set_var("section_id", $topic_array[section_id]);
$t->set_var("topic_id", $topic_id);
$t->pparse('content', 'buttons');
// END DISPLAY TOP SET OF BUTTONS
// forward and back
// Create Next / Prev Links and $Result_Set Value
$browse_html = browse_links($total_messages, $limit, $page_length, $_SERVER['PHP_SELF'], $topic_array[section_id], $topic_id);
echo $browse_html;
// echo "DEBUG $owner_array[owner_name] and $owner_array[owner_id]";
// ##END DEBUG
// put all messages into an array so I can reverse it
// this would better be done in the SQL but I can't get my head round it today
$messages_to_show_array = array();
if (mysql_num_rows($messages_to_show)) {
    if ($current_message = mysql_fetch_array($messages_to_show))
        do {
        array_push($messages_to_show_array, $current_message);
    } while ($current_message = mysql_fetch_array($messages_to_show));
} 

if ($user_array[user_backwards] == "y") {
    $messages_to_show_array = array_reverse($messages_to_show_array);
}

unset($current_message);

if (mysql_num_rows($messages_to_show)) {
    // reverse array here for backwards people
    $t->set_block('topic_handle', 'CommentBlock', 'messagerow');

    foreach($messages_to_show_array as $current_message) {
        set_time_limit(60); 

        $t->set_var("username", get_username($current_message[user_id]));

        $t->set_var("section_id", $topic_array[section_id]);

        $t->set_var("user_moto", $current_message["message_popname"]);

        $nx_message = nx_code($current_message["message_text"]);
        $t->set_var("edit", $nx_message);

        $t->set_var("user_id", $current_message["user_id"]);
        $t->set_var("date", $current_message["format_time"]);

        $t->set_var("message_id", $current_message["message_id"]);
        $t->set_var("topic_id", $topic_id);
        if (strlen($current_message["message_title"])) {
            $t->set_var("subject", "<b>Subject:</b> " . $current_message["message_title"]);
        } else {
            $t->set_var("subject", "");
        } 

        $t->pparse('messagerow', 'CommentBlock', false);
    } 
    // now update last view time here
    subscribe_to_topic($topic_id, $_SESSION[current_id]);

   set_topicread($_SESSION[current_id],$topic_id);

} else {
    // No messages to display
} 
// DISPLAY BOTTOM SET OF BUTTONS
echo $browse_html;

if (($owner_array[owner_id] == $_SESSION[current_id]) or (is_sysop($_SESSION[current_id]))) {
    $t->set_file('buttons', 'readtopic_links_admin_end.html');
} else {
    if ($topic_array["topic_readonly"] == 'y') {
        $t->set_file('buttons', 'readtopic_links_readonly.html');
    } else {
        $t->set_file('buttons', 'readtopic_links.html');
    } 
} 
$t->set_var("section_id", $topic_array[section_id]);
$t->set_var("topic_id", $topic_id);
$t->pparse('content', 'buttons');
// END DISPLAY BOTTOM SET OF BUTTONS
page_end($breadcrumbs);

?>


























