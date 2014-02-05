<?php
#FIXME check for existing accounts!

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/site.php');

// parameters
if(isset($_POST['ValidChk']))
{
  $waiting_users_array = $_POST['ValidChk'];
}
else
{
  $waiting_users_array = false;
}

if(isset($_POST['Reject']))
{
  $reject = true;
}
else
{
  $reject = false;
}


if(isset($_POST['Accept']))
{
  $accept = true;
}
else
{
  $accept = false;
}


$db = opendata();
session_start();

// check login
if (!validlogin())
{
  eject_user();
}

if(!$user_array = get_user_array($_SESSION['current_id']))
{
  nexus_error();
}


// only sysops can approve accounts
if($user_array['user_sysop'] != 'y')
{
  header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().
	 "section.php?section_id=1");
}


if($waiting_users_array)
{

  foreach ($waiting_users_array as $waiting_user_id)
    {
      
 
      $current_user_array = get_invalid_user_array($waiting_user_id);
      // if validating

      if($accept)
	{
	  // clean up any oddness in the waiting account array
	  // I guess I should sort out this whole quoting thing in one place someday ...
	  
	  $current_user_array['user_name'] = escape_input($current_user_array['user_name']);
	  $current_user_array['user_email'] = escape_input($current_user_array['user_email']);
	  $current_user_array['user_password'] = escape_input($current_user_array['user_password']);
	  $current_user_array['user_realname'] = escape_input($current_user_array['user_realname']);
	  
	  $new_user_id = add_new_user($current_user_array);
	  catchup($new_user_id);
	  send_welcome_email($current_user_array);
	}

      // common things
      delete_invalid_user($current_user_array['user_id']);
      
      // mail summary to sysop
      if($accept)
	{
	  mail(SYSOP_MAIL, "New Account Created", 
	       "Added User:\n".print_r($current_user_array,true), 
	       "From: ".SYSOP_MAIL); 
	}
      else
	{
	  mail(SYSOP_MAIL, "New Account Rejected", 
	       "Rejected User:\n".print_r($current_user_array,true), 
	       "From: ".SYSOP_MAIL);
	  
	}
    }

  
}
else
{

  // show the no waiting accounts
  // no accounts are waiting to be processed

}

header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot().
       "validate_accounts.php");

?>
