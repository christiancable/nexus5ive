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
?>