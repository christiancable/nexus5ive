<?php

#high level interface functions, things that deal with templates and output to the screen

function display_message($message_id, $user_id, $template, $mode, $db)
{
#takes a message_id and template and the id of the current user and displays one message
  
  // fetch message
  $message_array = get_message_with_time($message_id);
  
  // set template file according to display mode
  switch ($mode) 
    {
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
    case "SECRET_OWNER_VIEW":
      $template->set_file('topic_handle','secret_owner_view.html');
      break;
    default:
      $template->set_file('topic_handle','normal_comment.html');
    }
  
  $template->set_block('topic_handle','CommentBlock','messagerow');
  
  // get author name
  
  $author=get_username($message_array['user_id']);
  $template->set_var("username",$author);
  
  $template->set_var("user_moto",$message_array['message_popname']);
  
  $nx_message = nx_code($message_array['message_text']);
  $template->set_var("edit",$nx_message);
  
  $template->set_var("user_id",$message_array['user_id']);
  $template->set_var("date",$message_array['format_time']);
  $template->set_var("message_id",$message_array['message_id']);
  $template->set_var("topic_id",$message_array['topic_id']);
  
  if(strlen($message_array["message_title"]))
    {
      $template->set_var("subject","<b>Subject:</b> ".$message_array['message_title']);
    }
  else
    {
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
  
  if($new_messages)
    {
      $template->set_file("header","mail_page.html");
    } 
  else
    {
      $template->set_file("header","page.html");
    }
  
  $template->set_var("breadcrumbs",$breadcrumbs);
  $template->set_var("pagetitle",$page_title);
  
  if($new_comments)
    {
      $template->set_var("user_name","<b>$user_name</b>");
    }
  else
    {
      $template->set_var("user_name","$user_name");
    }
  
  $template->set_var("user_popname",$user_popname);
  $template->set_var("user_id",$user_id);
  
  if($num_msg)
    {
      $template->set_var("num_msg",$num_msg);
    }
  else
    {
      $template->set_var("num_msg", "no");
    }
  $template->set_var("owner_id",$owner_id);
  $template->set_var("ownername",$owner_name);
  $template->pparse('output','header');
}



function display_topic($topic_array, $user_id, $template, $mode)
{
# outputs a topic menu entry
  
  
  // set template file according to display mode
  switch ($mode)
    {
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

  
  if($topic_array['topic_title_hidden']=='y')
    {
      $template->set_var("TOPICTITLE", "");
    } 
  else
    {
      $template->set_var("TOPICTITLE", $topic_array['topic_title']);
    }
  $template->set_var("TOPIC_ID", $topic_array['topic_id']);
  $template->set_var("SECTION_ID", $topic_array['section_id']);
  $template->set_var("TOPIC_TEXT", nx_code($topic_array['topic_description']));
  $template->pparse('output','topic_handle');
}



function display_sectionheader($section_array, $user_array, $template)
{
  /*
   * displays a menu entry for a subsection
   */	
  if (can_user_edit_section($user_array, $section_array)) 
    { 
# admin user
      
# check for existance of subsection
      $subsection_list_array = get_subsectionlist_array($section_array['section_id']);
      
      if (new_messages_in_section($user_array['user_id'], $section_array['section_id'])) 
	{
	  if($subsection_list_array)
	    {
	      $template->set_file('topic_handle','section_admin_new.html');
	    }
	  else 
	    {
	      $template->set_file('topic_handle','section_admin_new_empty.html');
	    }
	} 
      else
	{
	  if($subsection_list_array)
	    {
	      $template->set_file('topic_handle','section_admin.html');
	      
	    } 
	  else
	    {
	      $template->set_file('topic_handle','section_admin_empty.html');
	    }
	}
      
    }
  else
    {
# standard user
      
      if (new_messages_in_section($user_array['user_id'], $section_array['section_id'])) 
	{
	  $template->set_file('topic_handle','section_user_new.html');
	}
      else
	{
	  $template->set_file('topic_handle','section_user.html');
	}
      
    }	
	
  $template->set_var("section_id",$section_array['section_id']);
  $template->set_var("section_intro",$section_array['section_intro']);
  $template->set_var("section_title", $section_array['section_title']);
  if ($num = get_count_section_messages($section_array['section_id'])) 
    {
      $template->set_var("messages",$num);
    } 
  else {
    $template->set_var("messages","0");
  }
  
  $template->pparse('output','topic_handle');
  
} 



function page_end($breadcrumbs, $template)
{
  
  $template->set_file("PageEnd","page_end.html");
  $template->set_var("BREADCRUMBS",$breadcrumbs);
  $template->pparse("OutPut","PageEnd");
  
}


function browse_links($total_messages, $page_length, $start_message, $url, $topic_id)
{

  $LINKS_START= '<div class="navigation" align=center>';
  $LINKS_END='</div>';

  $Pages=$total_messages / $page_length;
  $page_num = ($start_message / $page_length)+1;  
  $page = " Page ".ceil($page_num)." of ".ceil($Pages)." ";

  if($start_message > 0)
    {
      $previous_start = $start_message - $page_length;
      if ( $previous_start < 0 )
	{
	  $previous_start = 0;
	}

      $previous_link = '<a href="readtopic.php?topic_id='.$topic_id.'&start_message='.$previous_start.'">[ << Previous ]</a>';

    }
  else 
    {
      $previous_link = "";
    }
  
  if( ($start_message + $page_length) < $total_messages)
    {
      $next_start = $start_message + $page_length;

      $next_link = '<a href="readtopic.php?topic_id='.$topic_id.'&start_message='.$next_start.'">[ Next >> ]</a>';
    }
  else 
    {
      $next_link = "";
    }
  
  
  return $LINKS_START . $previous_link . $page . $next_link . $LINKS_END;

}

function get_dummybreadcrumbs()
{
  
  $top_section_array = get_section(1);
  $breadcrumbs = '<font size="-1"><a href="section.php?section_id=1">'.$top_section_array['section_title'].'</a> -&gt; </font>';
  
  return $breadcrumbs;
  
}
?>
