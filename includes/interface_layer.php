<?php 
// high level interface functions, things that deal with templates and output to the screen
function display_message($message_id, $user_id, $template, $mode, $db)
{
	// takes a message_id and template and the id of the current user and displays one message 
	// fetch message
	$message_array = get_message_with_time($message_id); 
	// set template file according to display mode
	switch ($mode) {
		case "SECRET_OWNER":
			$template->set_file('topic_handle', 'secret_owner.html');
			break;
		case "SECRET_COMMENT":
			$template->set_file('topic_handle', 'secret_comment.html');
			break;
		case "NORMAL_OWNER":
			$template->set_file('topic_handle', 'normal_owner.html');
			break;
		case "NORMAL_COMMENT":
			$template->set_file('topic_handle', 'normal_comment.html');
			break;
		default:
			$template->set_file('topic_handle', 'normal_comment.html');
	} 

	$template->set_block('topic_handle', 'CommentBlock', 'messagerow'); 
	// get author name
	$author = get_username($message_array[user_id]);
	$template->set_var("username", $author); 
	// $template->set_var("section_id", $topic_array[section_id]);
	$template->set_var("user_moto", $message_array[message_popname]); 
	// replace emotes with html gubbings
	// $template->set_var("edit",$current_message["message_text"]);
	$nx_message = nx_code($message_array[message_text]);
	$template->set_var("edit", $nx_message);

	$template->set_var("user_id", $message_array["user_id"]);
	$template->set_var("date", $message_array["format_time"]);
	$template->set_var("message_id", $message_array["message_id"]);
	$template->set_var("topic_id", $message_array[topic_id]);
	$template->set_var("subject", $message_array["message_title"]);
	$template->pparse('messagerow', 'CommentBlock');
} 

function eject_user()
{
	/**
	 * ejects a user from the bbs
	 */

	$template_location = TEMPLATE_HOME . DEFAULT_TEMPLATE;

	$t = new Template($template_location);
	session_destroy();
	$t->set_file("MyFileHandle", "timeout.html");
	$t->set_var("REDIRECT_URL", "http://" . $_SERVER['HTTP_HOST'] . "/");
	$t->pparse("MyOutput", "MyFileHandle");
	exit();
} 

function page_end($breadcrumbs)
{
	$template_location = TEMPLATE_HOME . DEFAULT_TEMPLATE;

	$t = new Template($template_location);

	$t->set_file("PageEnd", "page_end.html");
	$t->set_var("BREADCRUMBS", $breadcrumbs);
	$t->pparse("OutPut", "PageEnd");
} 

?>