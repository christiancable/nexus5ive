<?php

# get form vars
$username = $_POST['username'];
$password = $_POST['password'];

// includes

include_once('../includes/common.php');
include_once('../includes/database_layer.php');
# include_once('../includes/interface_layer.php');
include_once('../includes/site.php');

if (!$db = opendata()) {
    show_error("$_SERVER[PHP_SELF]", "Could not connect to database");
}

if ($user_array = get_user_array_from_name($username)) {

    # check password
    #  if($user_array['user_password']<>$password)
    #echo "debug password";

    if (!checkPassword($user_array['user_id'], $password)) {
        header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."login_error.html");
        exit;
    }

    #check banned
    if ($user_array['user_banned']<>0) {
        $email_to = "sysop@nexus5.org.uk";
        $email_from = "From: nexus@nexus5.org.uk";
        $error_txt = " $username attempt from $_SERVER[REMOTE_ADDR]\n";
        $str = "[" . date("Y/m/d h:i:s", mktime()) . "] " . $error_txt;
        mail($email_to, "nexus alert", $str, $email_from);
        header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."banned.html");
        exit;
    }

    $current_id = $user_array['user_id'];
    /* session_register is gone! */
    
    /*
    session_register("current_id");
    session_register("my_theme");
    session_register("no_pictures");
    */
   
    session_start();

    $_SESSION['current_id'] = $user_array['user_id'];
    $_SESSION['my_theme'] = $user_array['user_theme'];
    $_SESSION['no_pictures'] = $user_array['user_no_pictures'];
   
    // increase number of times on nexus here
    $num_of_visits = $user_array['user_totalvisits']+1;
    setuser_logon($user_array['user_id'], $_SERVER['REMOTE_ADDR'], $num_of_visits);

    // cookies are yummy
    $cookie_data = array();
    $cookie_data['user_id'] = $user_array['user_id'];
    $cookie_data['password_hash'] = md5($user_array['user_password']);

    setcookie("user", serialize($cookie_data), time()+3600);
    header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."section.php?section_id=1");
    exit;

} else {

    # check username and password page
    # echo "final error";
    header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."/login_error.html");
}
