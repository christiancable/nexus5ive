<?php

#high level interface functions, things that deal with templates and output to the screen
# remove any deps to other libs here 

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


	if($topic_array[topic_title_hidden]=='y'){
		$template->set_var("TOPICTITLE", "");
	} else {
		$template->set_var("TOPICTITLE", $topic_array[topic_title]);
	}
	$template->set_var("TOPIC_ID", $topic_array[topic_id]);
	$template->set_var("SECTION_ID", $topic_array[section_id]);
	$template->set_var("TOPIC_TEXT", nx_code($topic_array[topic_description]));
	$template->pparse('output','topic_handle');
}

function display_sectionheader($section_array, $user_array, $template)
{
/*
* displays a menu entry for a subsection
*/	
	if (can_user_edit_section($user_array, $section_array)) { # admin user

		# check for existance of subsection
		$subsection_list_array = get_subsectionlist_array($section_array[section_id]);

		if (new_messages_in_section($user_array[user_id], $section_array[section_id])) {
			if($subsection_list_array){
				$template->set_file('topic_handle','section_admin_new.html');
			} else {
				$template->set_file('topic_handle','section_admin_new_empty.html');
			}
		} else {
			if($subsection_list_array){
				$template->set_file('topic_handle','section_admin.html');

			} else {
				$template->set_file('topic_handle','section_admin_empty.html');
			}
		}
	    
	} else { # standard user
	
		if (new_messages_in_section($user_array[user_id], $section_array[section_id])) {
			$template->set_file('topic_handle','section_user_new.html');
		} else {
			$template->set_file('topic_handle','section_user.html');
		}
	
	}	
	
	$template->set_var("section_id",$section_array[section_id] );
	$template->set_var("section_intro",$section_array[section_intro]);
	$template->set_var("section_title", $section_array[section_title]);
	if ($num = get_count_section_messages($section_array[section_id])) {
		$template->set_var("messages",$num);
	} else {
		$template->set_var("messages","0");
	}
	
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
	$t->set_var("REDIRECT_URL","http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/");
	$t->pparse("MyOutput","MyFileHandle");
    exit();
}

function page_end($breadcrumbs, $template){

#	$template_location = TEMPLATE_HOME.DEFAULT_TEMPLATE;

#	$t = new Template($template_location);

	$template->set_file("PageEnd","page_end.html");
	$template->set_var("BREADCRUMBS",$breadcrumbs);
	$template->pparse("OutPut","PageEnd");

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
		$replacement = '<img src="'.$emotes.'big_smile.png'.'" alt=":-D">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?\)/';
		$replacement = '<img src="'.$emotes.'smile.png'.'" alt=":-)">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/;-? ?\)/';
		$replacement = '<img src="'.$emotes.'wink.png'.'" alt=";-)">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?\[/';
		$replacement = '<img src="'.$emotes.'confused.png'.'" alt=":-\">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/8-? ?\)/';
		$replacement = '<img src="'.$emotes.'cool.png'.'" alt="8-)">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?@/';
		$replacement = '<img src="'.$emotes.'angry.png'.'" alt=":-@">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/\bLOL\b/i';
		$replacement = '<img src="'.$emotes.'lol.png'.'" alt="LOL">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?\(/';
		$replacement = '<img src="'.$emotes.'sad.png'.'" alt=":-(">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?P/'; # will this match lower case?
		$replacement = '<img src="'.$emotes.'tongue_out.png'.'" alt=":-P">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?0/';
		$replacement = '<img src="'.$emotes.'ooooh.png'.'" alt=":-0">' ;
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = '/:-? ?o/i';
		$replacement = '<img src="'.$emotes.'ooooh.png'.'" alt=":-0">' ;
		$text = preg_replace($pattern, $replacement, $text);
	}


	return $text;


}


function get_dummybreadcrumbs(){

  $top_section_array = get_section(1);
  $breadcrumbs = '<font size="-1"><a href="section.php?section_id=1">'.$top_section_array[section_title].'</a> -&gt; </font>';

  return $breadcrumbs;

}
?>
