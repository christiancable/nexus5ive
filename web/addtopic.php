<?php
// add new topic interface code

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


// parameters
$section_id = $_GET['section_id'];

$db = opendata();
session_start();
$template_location = TEMPLATE_HOME . $_SESSION['my_theme'];
$t = new Template($template_location);

if (!validlogin()) 
{
  show_error($_SERVER['PHP_SELF']."validlogin failed<br>SESSION is ".print_r($_SESSION,true));
} 

if (!$user_array = get_user_array($_SESSION['current_id'])) 
{
  show_error($_SERVER['PHP_SELF']."get_user_array failed<br>SESSION is ".print_r($_SESSION,true));
} 

if (!$section_array = get_section($section_id))
{
  show_error($_SERVER['PHP_SELF']."get_section failed<br>section_id is $section_id ");
}

if (!$moderator_name = get_username($section_array['user_id'])) 
{
  show_error($_SERVER['PHP_SELF']."get_username failed for moderator<br>section_array is ".print_r($section_array, true));
} 

# can user add a topic to this section
if (!can_user_edit_section($user_array, $section_array))
{
# section exists but the user can not add topics here
  header("Location: http://" . $_SERVER['HTTP_HOST'] .get_bbsroot(). "section.php?section_id=1");
  exit();
}

if (!$breadcrumbs = get_breadcrumbs_topic($section_array['section_id']))
{
  show_error($_SERVER['PHP_SELF']."get_breadcrumbs_topic failed <br>section_array is ".print_r($section_array, true));
}

/* new header code */
display_header($t,
	       $breadcrumbs,
	       $section_array['section_title'],
	       $user_array['user_name'],
	       $user_array['user_popname'],
	       $_SESSION['current_id'],
	       count_instant_messages($_SESSION['current_id']),
	       $section_array['user_id'],
	       $moderator_name,
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

$t -> set_file("topicform", "addtopic.html");
$t -> set_var("SECTION_ID", $section_array['section_id']);
// SELECT
$select_code = '<option value="' . $section_array['section_id'] . '">' . $section_array['section_title'] . '</option>';

$sectionlist_array = get_sectionlist_array($user_array);

foreach ($sectionlist_array as $current_element) 
{
  $select_code = $select_code . '<option value="' . $current_element['section_id'] . 
    '">' . $current_element['section_title'] . '</option>';
} 

$t -> set_var("SELECT_CODE", $select_code);

$t -> pparse("TopicOutput", "topicform");

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

page_end($breadcrumbs, $t);
?>