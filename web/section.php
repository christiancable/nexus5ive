<?php

include('../includes/database.php');

$db = opendata();
session_start();
$template_location = TEMPLATE_HOME . $my_theme;
// check login

if (!validlogin()) {
    eject_user();
}
// ADD CHECKS HERE
$user_array = get_user_array($_SESSION[current_id]);

// temp - transsistional vars
if (isset($section_id))
    $section = $section_id;
// end temp


if (!$sectioninfodetails = get_section($section)) {
    nexus_error();
}

$sectionname = $sectioninfodetails["section_title"];
$sectionowner = $sectioninfodetails["user_id"];
$sectionparent = $sectioninfodetails["parent_id"];

$breadcrumbs = get_breadcrumbs($section);

$location_str = '<a href="section.php?section=' . $sectioninfodetails[section_id] . '">' . $sectionname . '</a>';


update_location($location_str);

if(!$ownername = get_username($sectionowner)){
	$ownername = "unknowm moderator";
}

$t = new Template($template_location);


display_header($t,
	       $breadcrumbs,
	       $sectionname,
	       $user_array["user_name"],
	       $user_array["user_popname"],
	       $_SESSION[current_id],
	       count_instant_messages($_SESSION[current_id]),
	       $sectionowner,
	       $ownername,
	       get_count_unread_comments($_SESSION[current_id]),
	       get_count_unread_messages($_SESSION[current_id]));


#$t->set_var("section_id", $section);

if(is_section_owner($sectioninfodetails[section_id], $user_array[user_id], $db)) {
    $t->set_file('top_links', 'menu_topic_links_admin.html');
} else {
    $t->set_file('top_links', 'menu_topic_links.html');
}

$t->set_var("section_id", $section);
$t->pparse('content', 'top_links');
// END DISPLAY TOP SET OF BUTTONS

// ## topics


if ($topics_list = get_section_topics($section) ) {
   		
	foreach ($topics_list as $current_topic_array){

		if(new_messages_in_topic($current_topic_array[topic_id], $_SESSION[current_id])){

			if(can_user_edit_topic($user_array, $current_topic_array)){

				if(!unsubscribed_from_topic($current_topic_array[topic_id], $_SESSION[current_id])){
					$mode = "NEW_ADMIN_SUB";
				} else {
					$mode = "NEW_ADMIN_UNSUB";

				}

			} else {

				if(!unsubscribed_from_topic($current_topic_array[topic_id], $_SESSION[current_id])){
					$mode = "NEW_NORMAL_SUB";
				} else {
					$mode = "NEW_NORMAL_UNSUB";

				}
			}

		} else {

			if(can_user_edit_topic($user_array, $current_topic_array)){

				if(!unsubscribed_from_topic($current_topic_array[topic_id], $_SESSION[current_id])){
					$mode = "ADMIN_SUB";
				} else {
					$mode = "ADMIN_UNSUB";

				}

			} else {

				if(!unsubscribed_from_topic($current_topic_array[topic_id], $_SESSION[current_id])){
					$mode = "NORMAL_SUB";
				} else {
					$mode = "NORMAL_UNSUB";

				}
			}




		}

	display_topic($current_topic_array, $_SESSION[current_id], $t, $mode);

	}

}


// ########## subsections

# this bit here is a mess, we have to ship this off to templates asap
$subsectionlist = get_subsectionlist_array($section);

#echo "<!-- looking for subsections of $section_id section -->";

if ($subsectionlist) {
    // table begin
    $count = 0;

    echo '<TABLE width="100%" border="0">';

    foreach ($subsectionlist as $current_sub_section_array){
	if ($count % 2 == 0) {
		echo "<TR VALIGN=TOP>";
		}
		
        #sectionheader($current_sub_section_array);
		display_sectionheader($current_sub_section_array, $user_array,$t);
        // }
        
        $count ++;
    }
    if ($count % 2) {
        echo "<TD></TD>";
    }
    // table end
    // check if we need a filler cell
    echo "</TABLE>";
} else {
    // no subsections
}


// BEGIN DISPLAY TOP SET OF BUTTONS
if (is_section_owner($sectioninfodetails[section_id], $user_array[user_id], $db)) {
    $t->set_file('bottom_links', 'menu_menu_links_admin.html');
} else {
    $t->set_file('bottom_links', 'menu_topic_links.html');
} 
$t->set_var("SECTION_ID", $section);
$t->pparse('content', 'bottom_links');

page_end($breadcrumbs,$t);

?>
