<?php

#high level interface functions, things that deal with templates and output to the screen

function display_message($message_id, $user_id, $template, $mode, $db){
#takes a message_id and template and the id of the current user and displays one message

	// fetch message
	$message_array = get_message_with_time($message_id);

	// set template file according to display mode
	switch ($mode) {
    case "SECRET_OWNER":
     	$template->set_file('topic_handle','secret_owner.html');
        break;
    case "SECRET_COMMENT":
		$template->set_file('topic_handle','secret_comment.html');
        break;
    case "NORMAL_OWNER":
		$template->set_file('topic_handle','normal_owner.html');
        break;
	case "NORMAL_COMMENT":
		$template->set_file('topic_handle','normal_comment.html');
		break;
    default:
	    $template->set_file('topic_handle','normal_comment.html');
	}

   $template->set_block('topic_handle','CommentBlock','messagerow');

   // get author name

	$author=get_username($message_array[user_id]);
	$template->set_var("username",$author);

	//$template->set_var("section_id", $topic_array[section_id]);
	$template->set_var("user_moto",$message_array[message_popname]);

	//replace emotes with html gubbings
	//$template->set_var("edit",$current_message["message_text"]);

	$nx_message = nx_code($message_array[message_text]);
	$template->set_var("edit",$nx_message);

	$template->set_var("user_id",$message_array["user_id"]);
	$template->set_var("date",$message_array["format_time"]);
	$template->set_var("message_id",$message_array["message_id"]);
	$template->set_var("topic_id",$message_array[topic_id]);

	if(strlen($message_array["message_title"])){
		$template->set_var("subject","<b>Subject:</b> ".$message_array["message_title"]);
	}else{
		$template->set_var("subject","");
	}
    $template->pparse('messagerow','CommentBlock');
}


function display_header($template,
			$breadcrumbs,
			$page_title,
			$user_name,
			$user_popname,
			$user_id,
			$num_msg,
			$owner_id,
			$owner_name,
			$new_comments,
			$new_messages)
{

	if($new_messages){
		$template->set_file("header","mail_page.html");
	} else {
		$template->set_file("header","page.html");
	}

	$template->set_var("breadcrumbs",$breadcrumbs);
	$template->set_var("pagetitle",$page_title);

	if($new_comments){
		$template->set_var("user_name","<b>$user_name</b>");
	} else {
		$template->set_var("user_name","$user_name");
	}

	$template->set_var("user_popname",$user_popname);
	$template->set_var("user_id",$user_id);

	if($num_msg)
		$template->set_var("num_msg",$num_msg);
	else
		$template->set_var("num_msg", "no");
	$template->set_var("owner_id",$owner_id);
	$template->set_var("ownername",$owner_name);
	$template->pparse('output','header');
}

function display_topic($topic_array, $user_id, $template, $mode){
	# outputs a topic menu entry


	// set template file according to display mode
	switch ($mode) {
		case "NEW_ADMIN_SUB":
			$template->set_file('topic_handle','topic_new_admin_sub.html');
			break;
		case "NEW_ADMIN_UNSUB":
			$template->set_file('topic_handle','topic_new_admin_unsub.html');
			break;
		case "NEW_NORMAL_SUB":
			$template->set_file('topic_handle','topic_new_sub.html');
			break;
		case "NEW_NORMAL_UNSUB":
			$template->set_file('topic_handle','topic_admin_unsub.html');
			break;
		case "ADMIN_SUB":
			$template->set_file('topic_handle','topic_admin_sub.html');
			break;
		case "ADMIN_UNSUB":
			$template->set_file('topic_handle','topic_admin_unsub.html');
			break;
		case "NORMAL_SUB":
			$template->set_file('topic_handle','topic_sub.html');
			break;
		case "NORMAL_UNSUB":
			$template->set_file('topic_handle','topic_unsub.html');
			break;

		default:
			$template->set_file('topic_handle','topic_sub.html');
	}


	$template->set_var("TOPICTITLE", $topic_array[topic_title]);
	$template->set_var("TOPIC_ID", $topic_array[topic_id]);
	$template->set_var("SECTION_ID", $topic_array[section_id]);
	$template->set_var("TOPIC_TEXT", nx_code($topic_array[topic_description]));
	$template->pparse('output','topic_handle');
}


function eject_user(){
	/*
	 ejects a user from the bbs
	*/

	$template_location = TEMPLATE_HOME.DEFAULT_TEMPLATE;

	$t = new Template($template_location);
	session_destroy();
	$t->set_file("MyFileHandle", "timeout.html");
	$t->set_var("REDIRECT_URL","http://".$_SERVER['HTTP_HOST']."/");
	$t->pparse("MyOutput","MyFileHandle");
    exit();
}

function page_end($breadcrumbs){

	$template_location = TEMPLATE_HOME.DEFAULT_TEMPLATE;

	$t = new Template($template_location);

	$t->set_file("PageEnd","page_end.html");
	$t->set_var("BREADCRUMBS",$breadcrumbs);
	$t->pparse("OutPut","PageEnd");

}

function browse_links($total_messages, $limit, $page_length, $current_url, $section_id, $topic_id){

$html_code = "";

if ($total_messages>0){
   $html_code .= "<div align=center><font size=-1>";
   if ($limit < $total_messages && $limit > 0)
      {
      $Res1=$limit-$page_length;
      $html_code .= "<A HREF=\"$current_url?limit=$Res1&section_id=$section_id&topic_id=$topic_id\">[ << Previous Page ]</A> ";
      }
   
   $Pages=$total_messages / $page_length;

   $page_num = 1+ ($limit / $page_length);  
   $html_code .= "Page ".ceil($page_num)." of ".ceil($Pages);

   if ($Pages>1){
      for ($b=0,$c=1; $b < $Pages; $b++,$c++){
	          $Res1=$page_length * $b;
			  # not sure I need this bit anymore - cfc
			  #if ($c == $Pages) {
			  #		echo "<A HREF=\"pages.php?limit=$Res1&section_id=$topic_array[section_id]&topic_id=$topic_id\"><b>[$c]></b></A> \n";   
			  #	} else {
		       #   	echo "<A HREF=\"pages.php?limit=$Res1&section_id=$topic_array[section_id]&topic_id=$topic_id\">[$c]</A> \n";
			  #}
          }
      }
 
   if ($limit>=0 && $limit<$total_messages)
      {
      $Res1=$limit+$page_length;
      if ($Res1<$total_messages)
         {
         $html_code .= " <A HREF=\"$current_url?limit=$Res1&section_id=$section_id&topic_id=$topic_id\">[ Next Page >> ]</A>";
         }
      }
	$html_code .= "</font></div>";   

	}

return $html_code;

}

function emote_text($text){

	#update this, make it part of the theme
	$emotes = "/emotes/$_SESSION[my_theme]/";

	# check to see if the user has turned off pictures here
	#if user has pictures turned off

	# WORD BOUNDARIES!

	if($_SESSION[no_pictures]<>'n') {
		# text mode faces
	} else {

		$pattern = '/:-? ?D/i';
		$replacement = '<img src="'.$emotes.'big_smile.png'.'">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?\)/';
		$replacement = '<img src="'.$emotes.'smile.png'.'">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/;-? ?\)/';
		$replacement = '<img src="'.$emotes.'wink.png'.'">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?\[/';
		$replacement = '<img src="'.$emotes.'confused.png'.'">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/8-? ?\)/';
		$replacement = '<img src="'.$emotes.'cool.png'.'">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?@/';
		$replacement = '<img src="'.$emotes.'angry.png'.'">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/\bLOL\b/i';
		$replacement = '<img src="'.$emotes.'lol.png'.'">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?\(/';
		$replacement = '<img src="'.$emotes.'sad.png'.'">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?P/'; # will this match lower case?
		$replacement = '<img src="'.$emotes.'tounge_out.png'.'">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?0/';
		$replacement = '<img src="'.$emotes.'ooooh.png'.'">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?o/i';
		$replacement = '<img src="'.$emotes.'ooooh.png'.'">' ;
		$text = preg_replace($pattern, $replacement, $text);
	}


	return $text;


}
?>
