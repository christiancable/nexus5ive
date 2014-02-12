<?php
/**********************************************/
/* process_adduser.php                        */
/*                                            */
/* adds account to waiting_accounts table or  */
/* returns user to the signup page with an    */
/* error                                      */
/**********************************************/

//includes
include_once('../includes/common.php');
include_once('../includes/database_layer.php');
include_once('../includes/interface_layer.php');
include_once('../includes/site.php');


//parameters
$error = false;

$db=opendata();


if (isset($_POST['username'])) {
    $username = $_POST['username'];
    if (strlen($username) < 1) {
        $error = 'missing';
    }
} else {
    $error = 'missing';
}

if (isset($_POST['realname'])) {
    $realname = $_POST['realname'];
    if (strlen($realname) < 1) {
        $error = 'missing';
    }
} else {
    $error = 'missing';
}


if (isset($_POST['email'])) {
    $email = $_POST['email'];
    if (strlen($email) < 1) {
        $error = 'missing';
    }
} else {
    $error = 'missing';
}

if (isset($_POST['hideemail'])) {
    $hideemail = $_POST['hideemail'];
} else {
    $error = 'missing';
}

if (isset($_POST['password'])) {
    $password = $_POST['password'];
    if (strlen($password) < 1) {
        $error = 'missing';
    }
} else {
    $error = 'missing';
}


if (isset($_POST['password_confirm'])) {
    $password_confirm = $_POST['password_confirm'];
    if (strlen($password_confirm) < 1) {
        $error = 'missing';
    }
} else {
    $error = 'missing';
    echo "$error is ".$error;
}

if (isset($_POST['age_confirm'])) {
    $age_confirm = $_POST['age_confirm'];
} else {
    $error = 'age';
}

// check passwords match
if ($password != $password_confirm) {
    $error = 'password';
}


// check for already existing user
if (lookup_username($username)) {
    $error = "existing_user";
}

// check for already existing email address
if (lookup_user_email($email)) {
    $error = "existing_email";
}

if ($error) {
    // if error redirect back to sign up page
    header('Location: http://'.$_SERVER['HTTP_HOST'].get_bbsroot().'adduser.php?error='.$error);
    exit();
}


// add user to the waiting_accounts table

// fill array with new details

$new_user_array = array();

$new_user_array['user_name'] = $username;
$new_user_array['user_email'] = $email;
$new_user_array['user_password'] = $password;
$new_user_array['user_realname'] = $realname;
$new_user_array['user_hideemail'] = $hideemail;
$new_user_array['user_ipaddress'] = $_SERVER['REMOTE_ADDR'];


if (!$new_invalid_user_id = add_invalid_user($new_user_array)) {
    // error
} else {
    if (AUTO_VALIDATE_USERS) {
        // validate account
        $invalid_user = get_invalid_user_array($new_invalid_user_id);

        if (!$new_user_id = add_new_user($invalid_user)) {
            // account not added
        }

        if (!delete_invalid_user($new_invalid_user_id)) {
          // invalid account not deleted
        }

        // send welcome email
        send_welcome_email($invalid_user);
     
        // set all topics as read to current date
        catchup($new_user_id);

        // redirect to the thank you your account is ready page
        header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."welcome.php?status=ready");

    } else {
        // redirect to thank you your account will be validated page
        header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."welcome.php?status=validate");
        // user will be validated by sysop later
    }

  // send summary to the sysop
  
    if (AUTO_VALIDATE_USERS) {
        mail(SYSOP_MAIL, "New Account Created", "Added User:\n".print_r($new_user_array, true), "From: ".SYSOP_MAIL);
    } else {
        mail(SYSOP_MAIL, "Account Waiting", "Waiting to Validate User:\n".print_r($new_user_array, true), "From: ".SYSOP_MAIL);
    }
}
