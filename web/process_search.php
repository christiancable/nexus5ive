<?php
// displays search form and 
// paginates any existing results

// cfc - may 2004

// parameters

if(isset($_POST['search']))
{
  $search_words = $_POST['search'];
}
else
{
  $search_words = false;
}

if(isset($_POST['phrase']))
{
  $phrase_search = true;
}
else
{
  $phrase_search = false;
}
// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


//common stuff
$db = opendata();
session_start();
$template_location =TEMPLATE_HOME.$_SESSION['my_theme']; 

// check login
if (!validlogin())
{
  eject_user();	
}

//update user activity



$user_array = get_user_array($_SESSION['current_id']);

update_location('<a href="search.php">Search</a>');

$users_on_array = get_users_online($_SESSION['current_id'], true);

$breadcrumbs = get_dummybreadcrumbs();

$t = new Template($template_location);

display_header($t,
	       $breadcrumbs,
	       "Search",
	       $user_array['user_name'],
	       $user_array['user_popname'],
	       $_SESSION['current_id'],
	       count_instant_messages($_SESSION['current_id']),
	       SYSOP_ID,
	       SYSOP_NAME,
	       get_count_unread_comments($_SESSION['current_id']),
	       get_count_unread_messages($_SESSION['current_id']));

display_navigationBar(
                      $topicleap=true,
                      $whosonline=true,
                      $mainmenu=false,
                      $examineuser=true,
                      $returntosection=false,
		      
                      $createtopic=false,
                      $createmenu=false,
                      $postcomment=false,

                      $section_id=false,
                      $parent_id=false,
                      $topic_id=false
                      );

$num_of_users = count($users_on_array);

  
$t->set_file('search', 'search.html');


$t->set_var('INITIAL_SEARCH',stripslashes($search_words));

if($search_words)
{
  $token_array = array();


  $search_token = strtok($search_words, ' ');
  
  if($phrase_search)
    {
      // add the phrase to the token_array
      array_push($token_array, $search_words);
    }
  else
    {
      while($search_token)
	{
	  // put each token into the token array and remove duplicates
	  array_push($token_array, $search_token);
	  $search_token = strtok(' ');
	}
    }
}



if($token_array)
{

  $search_results = message_search($token_array);
  $t->set_var("RESULTS_FOUND", count($search_results). " posts found");
  $t->set_block('search','ChunkBlock','resultrow');
  foreach($search_results as $current_result)
    {
      // get message
      $message_array = get_message_with_time($current_result);
      $topic_array = get_topic($message_array['topic_id']);
      $result_breadcrumbs = get_breadcrumbs_topic($topic_array['section_id']);
      $author=get_username($message_array['user_id']);

      // get position of message in topic
      $topic_posts_array = get_topic_posts($message_array['topic_id']);
      $post_position = array_search($message_array['message_id'],$topic_posts_array);
      
      // parse result
      $t->set_var('START_MESSAGE',$post_position);
      $t->set_var('POST_BREADCRUMBS', $result_breadcrumbs);
      if ($topic_array['topic_annon']<>'n')
	{
	  $t->set_var('POST_USER',"<b>Hidden</b>");
	}
      else
	{
	  $t->set_var('POST_USER',"<b>$author</b>");
	}
      $t->set_var('POST_DATE',$message_array['format_time']);
      $t->set_var('TOPIC_ID',$message_array['topic_id']);
      $post_summary = substr(strip_tags($message_array['message_text']), 0,150).'...';
      $t->set_var("POST_SUMMARY",$post_summary);
      if(strlen($message_array["message_title"]))
	{
	  $t->set_var('POST_SUBJECT',' - '.$message_array['message_title']);
	}
      else
	{
	  $t->set_var('POST_SUBJECT','');
	}

      if(strlen($topic_array['topic_title']))
        {
	  $t->set_var('TOPIC_TITLE',$topic_array['topic_title']);
        }
      else
        {
	  $t->set_var('TOPIC_TITLE','<i>Untitled</i>');
        }


      $t->parse('resultrow','ChunkBlock',true);
      
    }

}
$t->pparse('output','search');

display_navigationBar(
                      $topicleap=true,
                      $whosonline=true,
                      $mainmenu=false,
                      $examineuser=true,
                      $returntosection=false,

                      $createtopic=false,
                      $createmenu=false,
                      $postcomment=false,

                      $section_id=false,
                      $parent_id=false,
                      $topic_id=false
                      );


page_end($breadcrumbs,$t);
?>






