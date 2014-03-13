<?php
/* add functions checked for usage - 25/02/2014 */

  // general functions

  // needed libs
require_once '../phplib/php/template.inc';

// error reporting settings - 20091008

// error_reporting(E_ALL | E_STRICT);
error_reporting(-1);
ini_set("display_errors", "off");
ini_set("log_errors", "on");
# ini_set("error_log", "/home/fraggle/nexus/logs/nexus-php-error.txt");
ini_set("error_log", "../logs/nexus-php-error.txt");

// functions



function nexus_error($info = "")
{
    if ($info) {
        echo "$info";
    }
    eject_user();
}

function eject_user()
{
    /*
    generic timeout error, consider combining this with show_error function
    */
    header("Location: http://".$_SERVER['HTTP_HOST'].get_bbsroot()."timeout.html");
    exit();
}

function show_error($function, $message)
{

# display a brief error message to the user
    $error_txt =  "<h1>Problem Report</h1>";
    $error_txt = $error_txt . "error in <b>$function</b><br/>";
    $error_txt = $error_txt .  "message is <b>$message</b><br/>";
    $error_txt = $error_txt .  'please report this message <a href="mailto:'.SYSOP_MAIL.'">here</a>';
    $error_txt = $error_txt . '<br/><br/>';
    $error_txt = $error_txt .  '<a href="http://'.$_SERVER['HTTP_HOST'].get_bbsroot().'">Restart</a>';
    echo $error_txt;

#email the error to the sysop if the ERROR_MAIL flag is set
    if (ERROR_MAIL) {
        mail(SYSOP_MAIL, "Error Report", $error_txt, "From: ".SYSOP_MAIL);
    }
    exit();
}


function nx_code($text)
{

  // this function gets the data ready from printing on the screen via html

    if ($_SESSION['no_pictures']<>'n') {

        $pattern = '/<\s*img\s*src\s*=\s*\"(.*?)".*?>/i';
        $replacement = '[PICTURE-]$1[-PICTURE]';
        $text = preg_replace($pattern, $replacement, $text);

        $pattern = '#<a href="(.*)">\[PICTURE-\](.*)\[-PICTURE\]</a>#sSi';
        $replacement = '<a href="$1"><b>[ Link to $1 ]</b></a><br/>[PICTURE-]$2[-PICTURE]';
        $text = preg_replace($pattern, $replacement, $text);

    }

    $pattern ="/\[PICTURE\-\](.*)\[\-PICTURE\]/Ui";

    if ($_SESSION['no_pictures']<>'n') {
        $replacement = '<a href="'."$1".'" target="_blank">[Click Here To See '."$1".']</a>';
    } else {
        $replacement = '<img src="'."$1".'" alt="'."$1".'">' ;
    }
    $text = preg_replace($pattern, $replacement, $text);

    $pattern ="/\[WWW\-\](.*)\[\-WWW\]/Ui";
    $replacement = '<a href="'."$1".'" target="_blank">['."$1".']</a>';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern ="#\[I\-\](.*)\[\-I\]#isU";
    $replacement = '<I>'."$1".'</I>';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern ="#\[B\-\](.*)\[\-B\]#isU";
    $replacement = '<B>'."$1".'</B>';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern ="#\[ASCII\-\](.*)\[\-ASCII\]#isU";
    $replacement = '<pre> '."$1".'</pre>';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern ="#\[U\-\](.*)\[\-U\]#isU";
    $replacement = '<u>'."$1".'</u>';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern ="#\[SMALL\-\](.*)\[\-SMALL\]#isU";
    $replacement = '<p style="font-size:xx-small">'."$1".'</p>';
    $text = preg_replace($pattern, $replacement, $text);

    $text = nl2br($text);

  // now remove <br /> tags from preformatted blocks

    $pattern ="#\<pre>(.*)</pre>#isU";

    $text = preg_replace_callback(
        $pattern,
        create_function(
            '$matches',
            'return str_replace("<br />","",$matches[0]);'
        ),
        $text
    );

    $pattern ="#\[QUOTE\-\](.*)\[\-QUOTE\]#isU";
    $replacement = '<div class="quote">'."$1".'</div>';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern ="#\[UPDATED\-\](.*)\[\-UPDATED\]#isU";
    $replacement = '<div class="updated">'."$1".'</div>';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern ="#\[HUDSON\-\](.*)\[\-HUDSON\]#isU";
    $replacement = '<span class="spoiler">'."$1".'</span>';
    $text = preg_replace($pattern, $replacement, $text);

    $pattern ="#\[SPOILER\-\](.*)\[\-SPOILER\]#isU";
    $replacement = '<span class="spoiler">'."$1".'</span>';
    $text = preg_replace($pattern, $replacement, $text);

    # $pattern ="/\[youtube\-\](.*)\[\-youtube\]/Ui";
    # $replacement = '<b>YouTube support coming soon</b><br/><a href="'."$1".'" target="_blank">['."Click Here To Open In A New Window".']</a>';
    #$text = preg_replace($pattern, $replacement, $text);

    #$pattern = "#\[youtube\-\]http://(?:www\.)?youtube.com/watch\?v=(.*)\[-youtube\]#isU";
    $pattern = "#\[youtube\-\]http://(www\.)?youtube\.com/watch\?v=([a-zA-Z0-9\-_]+)\[-youtube\]#im";
    #$pattern ="/\[youtube\-\](.*)\[\-youtube\]/Ui";
    $replacement = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/'."$2".'"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/'."$2".'" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';

    $text = preg_replace($pattern, $replacement, $text);

    return $text;
}


function get_section_parents($section_id)
{
    /**
    * IMPUT topic_id
    * OUTPUT assoc array of topic_id and topic_title of all parent topics
    */

    $count = 0;
    $current_breadcrumb = get_section_parent_info($section_id);
    $breadcrumb_array[$count]['section_id'] = $current_breadcrumb['section_id'];
    $breadcrumb_array[$count]['section_title'] = $current_breadcrumb['section_title'];
    $count++;

    while ($current_breadcrumb = get_section_parent_info($current_breadcrumb['parent_id'])) {
        $breadcrumb_array[$count]['section_id'] = $current_breadcrumb['section_id'];
        $breadcrumb_array[$count]['section_title'] = $current_breadcrumb['section_title'];
        $count++;
    }

    if ($count) {
        return array_reverse($breadcrumb_array);
    } else {
        return false;
    }
}

function get_breadcrumbs($section)
{
  // update this when newsection is real
    $crumb_urls ='';
    $breadcrumbs = get_section_parents($section);
    $num_of_crumbs = count($breadcrumbs);

    for ($loop_count = 0; $loop_count < $num_of_crumbs-1; $loop_count++) {
        $crumb_urls .= '<a href="section.php?section_id=' . $breadcrumbs[$loop_count]["section_id"] . '">';
        if (strlen($breadcrumbs[$loop_count]["section_title"])) {
            $crumb_urls .= $breadcrumbs[$loop_count]["section_title"] . "</a> -&gt; ";
        } else {
            $crumb_urls .= " - " . "</a> -&gt; ";
        }
    }

    return $crumb_urls;
}

function get_breadcrumbs_topic($section)
{
  // update this when newsection is real

    $breadcrumbs = get_section_parents($section);
    $num_of_crumbs = count($breadcrumbs);
    $crumb_urls = "";

    for ($loop_count = 0; $loop_count < $num_of_crumbs; $loop_count++) {
        $crumb_urls .= '<a href="section.php?section_id=' . $breadcrumbs[$loop_count]['section_id'] . '">';
        if (strlen($breadcrumbs[$loop_count]["section_title"])) {
            $crumb_urls .= $breadcrumbs[$loop_count]["section_title"] . "</a> -&gt; ";
        } else {
            $crumb_urls .= " - " . "</a> -&gt; ";
        }
    }

    return $crumb_urls;
}

function get_bbsroot()
{
    $dirname = dirname($_SERVER['PHP_SELF']);
    if ($dirname != "/") {
        $dirname = dirname($_SERVER['PHP_SELF'])."/";
    } else {

    }

    return $dirname;
}

function send_welcome_email($invalid_user)
{
  // open file
    $welcome_message = file_get_contents("welcome_email.txt");

  // replace vars
    $welcome_message = str_replace("REALNAME", $invalid_user['user_realname'], $welcome_message);
    $welcome_message = str_replace("USERNAME", $invalid_user['user_name'], $welcome_message);
    $welcome_message = str_replace("PASSWORD", $invalid_user['user_password'], $welcome_message);

    mail(
        $invalid_user['user_email'],
        BBS_NAME." Account Request",
        $welcome_message,
        'From: '.SYSOP_MAIL
    );
  // send email
}

function escape_input($untrusted_string)
{
    return nl2br(htmlspecialchars($untrusted_string, ENT_NOQUOTES));
}

/* verified unused 25/02/2014

function send_banned_email($banned_user_name)
{
    $email_to = "sysop@nexus5.org.uk";
    $email_from = "From: nexus@nexus5.org.uk";
    $error_txt = " $banned_user_name  attempt from $_SERVER[REMOTE_ADDR]\n";
    $str = "[" . date("Y/m/d h:i:s", mktime()) . "] " . $error_txt;

    mail($email_to, "nexus alert", $str, $email_from);
}

function init_session()
{
}

*/
