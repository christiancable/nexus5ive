<?php

# ### Basic functions ####
/**
 * these all work on a single row of data
 */



# user account functions
function get_user_array($user_id)
{
    /**
     * takes user_id and returns an array of their userinfo
     * INPUT user_id
     * OUTPUT assoc array of user_info or false if user is not found
     */
     // get array
    $sql = "SELECT * FROM usertable WHERE user_id = $user_id";
    
     if (!$user_info = mysql_query($sql)){
         return false;
         }
    
     if (mysql_num_rows($user_info)){
         $user_array = mysql_fetch_array($user_info, MYSQL_ASSOC);
         return $user_array;
         }else{
         return false;
         }
     }


function add_user($user_array){
    /**
     * INPUT: user array
     * 
     * OUTPUT: true or false
     */
    
     $sql = "INSERT INTO usertable (user_name, user_email, user_comment, user_popname, user_password, user_realname, user_priv, user_display,
		user_backwards, user_sysop, user_location, user_theme, user_totaledits, user_totalvisits, user_status, user_banned, user_film, 
		user_band, user_sex, user_town, user_age, user_ipaddress) 
		values ('$user_array[user_name]', 
		        '$user_array[user_email]',
		        '$user_array[user_comment]',
			'$user_array[user_popname]',
			'$user_array[user_password]',
			'$user_array[user_realname]',
			'$user_array[user_priv]',
			'$user_array[user_display]',
			'$user_array[user_backwards]',
			'$user_array[user_sysop]',
			'$user_array[user_location]',
			'$user_array[user_theme]',
			'$user_array[user_totaledits]',
			'$user_array[user_totalvisits]',
			'$user_array[user_status]',
			'$user_array[user_banned]',
			'$user_array[user_film]',
			'$user_array[user_band]',
			'$user_array[user_sex]',
			'$user_array[user_town]',
			'$user_array[user_age]',
			'$user_array[user_ipaddress]')";
    
    
     if(mysql_query($sql)){
         return true;
         }else{
         echo $sql;
         return false;
         }
    
     }


function new_user_array(){
    /**
     * returns a blank user_array
     * 
     * not sure I like this as it takes away the point of the default values in the database .. hmmm
     */
     $user_array = array(
        'user_name' => 'change_me',
         'user_email' => '',
         'user_comment' => 'be nice to me I am new',
         'user_popname' => 'be nice to me I am new',
         'user_password' => '',
         'user_realname' => '',
         'user_priv' => '100',
         'user_display' => '20',
         'user_backwards' => 'n',
         'user_sysop' => 'n',
         'user_location' => '',
         'user_theme' => 'xp',
         'user_totaledits' => '0',
         'user_totalvisits' => '0',
         'user_status' => 'Invalid',
         'user_banned' => '0',
         'user_likes' => '',
         'user_hates' => '',
         'user_sex' => 'male',
         'user_town' => '',
         'user_age' => '13',
         'user_ipaddress' => '127.0.0.1');
    
     return $user_array;
    
     }


function update_user_array($user_array){
    /**
     * takes a user array and updates the database
     */
    
     $sql = "UPDATE usertable set 
		user_name = '$user_array[user_name]',
		user_email =  '$user_array[user_email]',
		user_comment = '$user_array[user_comment]',
		user_popname = '$user_array[user_popname]',
		user_password = '$user_array[user_password]',
		user_realname = '$user_array[user_realname]',
		user_priv = '$user_array[user_priv]',
		user_display ='$user_array[user_display]',
		user_backwards = '$user_array[user_backwards]',
		user_sysop = '$user_array[user_sysop]',
		user_location = '$user_array[user_location]',
		user_theme = '$user_array[user_theme]',
		user_totaledits = '$user_array[user_totaledits]',
		user_totalvisits = '$user_array[user_totalvisits]',
		user_status = '$user_array[user_status]',
		user_banned = '$user_array[user_banned]',
		user_film = '$user_array[user_film]',
		user_band = '$user_array[user_band]',
		user_sex = '$user_array[user_sex]',
		user_town = '$user_array[user_town]',
		user_age = '$user_array[user_age]',
		user_ipaddress = '$user_array[user_ipaddress]',
		user_no_pictures = '$user_array[user_no_pictures]'
		WHERE user_id = $user_array[user_id]";
    
    
    
     if(mysql_query($sql)){
         return true;
         }else{
         echo $sql;
         return false;
         }
     }

function lookup_user_id($username, $email){
    /**
     * looks for an existing user account with a given username or email address and returns user_id or false
     */
     $sql = "SELECT user_id FROM usertable WHERE user_name='$username' OR user_email='$email' LIMIT 1";
    
     if(!$query_result = mysql_query($sql)){
         return false;
         }else{
         if(!$result_array = mysql_fetch_array($query_result, MYSQL_ASSOC)){
             return false;
             }else{
             return $result_array[user_id];;
             }
         }
    
    
    
     }


function change_password($user_id, $new_password){
    
     $sql = "UPDATE usertable SET user_password='$new_password' WHERE user_id = '$user_id'";
    
     if(mysql_query($sql)){
         return true;
         }else{
         # echo $sql;
        return false;
         }
     }

# end user account functions
/**
 * messagetable functions
 * 
 * $message_array fields are assumed to be sql safe and escaped where needed
 */



function update_message($message_array){
    /**
     * INPUT message_array
     * 
     * OUTPUT true or false
     */
    
     if($message_array[message_time]){
         $sql = "UPDATE messagetable SET 
		message_text='$message_array[text]',
		topic_id='$message_array[topic_id]',
		user_id='$message_array[user_id]',
		message_title='$message_array[message_title]',
		message_time='$message_array[message_time]',
		message_popname='$message_array[message_popname]' 
		WHERE message_id='$message_array[message_id]'";
         }else{
         $sql = "UPDATE messagetable SET 
		message_text='$message_array[text]',
		topic_id='$message_array[topic_id]',
		user_id='$message_array[user_id]',
		message_title='$message_array[message_title]',
		message_popname='$message_array[message_popname]' 
		WHERE message_id='$message_array[message_id]'";
        
         }
    
     if(mysql_query($sql)){
         return true;
         }else{
         # echo "$sql";
        return false;
         }
    
     }

function delete_message($message_id){
    /**
     * INPUT message_id
     * 
     * OUTPUT true or false
     */
    
     $sql = "DELETE FROM messagetable WHERE message_id=$message_id";
    
     if(mysql_query($sql)){
         return true;
         }else{
         return false;
         }
    
    
     }

function add_message($message_array){
    /**
     * INPUT message_array
     * 
     * OUTPUT true or false
     */
     $sql = "INSERT INTO messagetable (message_text, topic_id, user_id, message_title, message_popname) values (
	'$message_array[message_text]',
	'$message_array[topic_id]',
	'$message_array[user_id]',
	'$message_array[message_title]',
	'$message_array[message_popname]')";
    
     if(mysql_query($sql)){
         return true;
         }else{
         return false;
         }
    
     }

function get_message($message_id){
    /**
     * INPUT message_id
     * 
     * OUTPUT message_array or false
     */
     $sql = "SELECT * FROM messagetable WHERE message_id=$message_id";
    
     if(!$message_result = mysql_query($sql)){
         return false;
         }else{
         if(!$message_array = mysql_fetch_array($message_result, MYSQL_ASSOC)){
             return false;
             }else{
             return $message_array;
             }
         }
    
     }

# #, DATE_FORMAT(message_time,\"%a %b %D - %H:%i %Y\")
function get_message_with_time($message_id){
    /**
     * INPUT message_id
     * 
     * OUTPUT message_array or false
     */
     $sql = "SELECT *, DATE_FORMAT(message_time,\"%a %b %D - %H:%i %Y\") AS format_time FROM messagetable WHERE message_id=$message_id";
    
     if(!$message_result = mysql_query($sql)){
         return false;
         }else{
         if(!$message_array = mysql_fetch_array($message_result, MYSQL_ASSOC)){
             return false;
             }else{
             return $message_array;
             }
         }
    
     }


/**
 * nexusmessagetable functions
 */

function update_nexusmessage($nexusmessage_array){
    
     }

function delete_nexusmessage($nexusmessage_id){
    
     }

function add_nexusmessage($nexusmessage_array){
    /**
     * INPUT message_array
     * 
     * OUTPUT true or false
     */
     $sql = "INSERT INTO nexusmessagetable (user_id, from_id,text) values (
	'$nexusmessage_array[user_id]',
	'$nexusmessage_array[from_id]',
	'$nexusmessage_array[text]')";
    
     # echo "debug $sql";
    if(mysql_query($sql)){
         return true;
         }else{
         return false;
         }
    
     }


function get_nexusmessage($nexusmessage_id){
    
     }

# ####
/**
 * topictable functions
 */

function update_topic($topic_array){
    /**
     * INPUT topic_array
     * 
     * OUTPUT true or false
     */
    
     $sql = "UPDATE topictable SET 
	topic_title=\"$topic_array[topic_title]\",
	section_id=$topic_array[section_id],
	topic_desctiption=\"$topic_array[topic_desctiption]\",
	topic_annon=\"$topic_array[topic_annon]\",
	topic_readonly=\"$topic_array[topic_readonly]\",
	topic_weight=\"$topic_array[topic_weight]\"
	WHERE topic_id=$topic_array[topic_id]";
    
     // echo "<h1>$sql</h1>";
    // exit();
    if(mysql_query($sql)){
         return true;
         }else{
         return false;
         }
     }

function delete_topic($topic_id){
    
    # removes a topic and all comments within
    
    $sql = "DELETE FROM messagetable WHERE topic_id=$topic_id";
     if(mysql_query($sql)){
         
	$sql = "DELETE FROM topictable WHERE topic_id=$topic_id";
         if(mysql_query($sql)){
             return true;
             }else{
             echo "$sql";
             exit();
             return false;
             }
         }else{        
         echo "$sql";
         exit();
         return false;
         }
    
     }



function add_topic($topic_array){
    /**
     * INPUT topic_array
     * 
     * OUTPUT true or false
     */
    
     $sql = "INSERT INTO topictable (topic_title, section_id, topic_desctiption, topic_annon, topic_readonly, topic_weight) 
	values (
	'$topic_array[topic_title]',
	'$topic_array[section_id]',
	'$topic_array[topic_desctiption]',
	'$topic_array[topic_annon]',
	'$topic_array[topic_readonly]',
	'$topic_array[topic_weight]'
	)";
    
     if(mysql_query($sql)){
         return true;
         }else{
         echo "$sql";
         exit();
         return false;
         }
     }


function get_topic($topic_id){
    
    
     $sql = "SELECT * FROM topictable WHERE topic_id=$topic_id";
    
     if(!$query_result = mysql_query($sql)){
         return false;
         }else{
         if(!$topic_array = mysql_fetch_array($query_result, MYSQL_ASSOC)){
             return false;
             }else{
             return $topic_array;
             }
         }
    
     }

# ## section functions
function add_section($section_array){
    /**
     * INPUT section_array
     * 
     * OUTPUT true or false
     */
    
    
    
     $sql = "INSERT INTO sectiontable (section_title, user_id, parent_id, section_weight, section_intro) values (
	'$section_array[section_title]',
	'$section_array[user_id]',
	'$section_array[parent_id]',
	'$section_array[section_weight]',
	'$section_array[section_intro]')";
    
     # echo "<h1>debug".$sql."</h1>";
    if(mysql_query($sql)){
         return true;
         }else{
         return false;
         }
    
     }














function get_section($section_id){
    
    
     $sql = "SELECT * FROM sectiontable WHERE section_id=$section_id";
    
     if(!$query_result = mysql_query($sql)){
         return false;
         }else{
         if(!$section_array = mysql_fetch_array($query_result, MYSQL_ASSOC)){
             return false;
             }else{
             return $section_array;
             }
         }
    
     }

function update_section($section_array){
    /**
     * INPUT message_array
     * 
     * OUTPUT true or false
     */
    
     $sql = "UPDATE sectiontable SET 
	section_title=\"$section_array[section_title]\",
	user_id=$section_array[user_id],
	parent_id=$section_array[parent_id],
	section_weight=$section_array[section_weight],
	section_intro=\"$section_array[section_intro]\"	
	WHERE section_id=$section_array[section_id]";
     if(mysql_query($sql)){
         return true;
         }else{
         return false;
         }
    
     }

# # end section functions
# # comment functions
# use the nexusmessage ones
function get_user_comment($user_id){
    /**
     * INPUT message_id
     * 
     * OUTPUT message_array or false
     */
    
     $instant_message_array = array();
    
    
     $sql = "SELECT comment_id, text, from_id, user_name FROM commenttable, usertable WHERE commenttable.user_id=$user_id AND usertable.user_id = from_id ORDER BY comment_id ASC";
    
     if(!$sql_result = mysql_query($sql)){
         return false;
        
         }else{
         if($current_array = mysql_fetch_array($sql_result)){
             do{
                 array_push($instant_message_array, $current_array);
                 } while ($current_array = mysql_fetch_array($sql_result));
            
             return $instant_message_array;
             }else{
             return false;
             }
        
         }
    
     }

function add_user_comment($comment_array){
    /**
     * INPUT message_array
     * 
     * OUTPUT true or false
     */
     $sql = "INSERT INTO commenttable (user_id, from_id,text) values (
	'$comment_array[user_id]',
	'$comment_array[from_id]',
	'$comment_array[text]')";
    
     # echo "debug $sql";
    if(mysql_query($sql)){
         return true;
         }else{
         return false;
         }
    
     }

function delete_user_comments($user_id){
    
     $sql = "DELETE FROM commenttable WHERE user_id=$user_id AND readstatus='y'";
    
     if(!mysql_query($sql)){
         return false;
         }else{
         return true;
         }
     }


function mark_comments_read($user_id){
    
     $sql = "UPDATE commenttable SET readstatus='y' WHERE user_id = $user_id";
    
     if(!mysql_query($sql)){
         return false;
         }else{
         return true;
         }
     }
# # end comment functions
# # topicview functions
function add_topicview($topicview_array){
    /**
     * INPUT message_array
     * 
     * OUTPUT true or false
     */
     $sql = "INSERT INTO topicview (user_id, topic_id, msg_date) VALUES (
	'$topicview_array[user_id]',
	'$topicview_array[topic_id]',
	'$topicview_array[message_time]')";
    
     if(mysql_query($sql)){
         return true;
         }else{
         return false;
         }
    
     }

function unsubscribed_from_topic($topic_id, $user_id){
    
     $sql = "SELECT unsubscribe FROM topicview WHERE user_id=$user_id AND topic_id=$topic_id";
    
    
     if(!$unsub_info = mysql_query($sql)){
         return false;
         }
    
     if(mysql_num_rows($unsub_info)){
        
         $unsub_result = mysql_fetch_array($unsub_info, MYSQL_ASSOC);
         return $unsub_result[unsubscribe];
        
         }else{
         return false;
         }
    
     }

function unsubscribe_from_topic($topic_id, $user_id){
    /**
     * INPUT topic_id and user_id
     * 
     * OUTPUT true or false
     */
    
     $sql = "UPDATE topicview SET unsubscribe=1 WHERE topic_id=$topic_id AND user_id=$user_id";
    
     if(mysql_query($sql)){
         return true;
         }else{
         return false;
         }
    
     }

function subscribe_to_topic($topic_id, $user_id){
    /**
     * INPUT topic_id and user_id
     * 
     * OUTPUT true or false
     */
    
     // if unsubed use insert rather than update
    if(unsubscribed_from_topic($topic_id, $user_id)){
         $sql = "INSERT INTO topicview SET (unsubscribe,topic_id,user_id) VALUES (0,$topic_id,$user_id)";
         }else{
         $sql = "UPDATE topicview SET unsubscribe=0 WHERE topic_id=$topic_id AND user_id=$user_id";
         }
    
    
     if(mysql_query($sql)){
         return true;
         }else{
         return false;
         }
    
     }





# ####### FUNCTIONS THAT USE JOINS
function get_count_section_messages($section_id){
     # returns number of messages in section
    $sql = "SELECT COUNT(messagetable.message_id) AS totalmsg FROM messagetable, topictable WHERE messagetable.topic_id = topictable.topic_id AND topictable.section_id = $section_id";
    
     if(!$count_result = mysql_query($sql)){
         return false;
         }else{
         if(!$count_array = mysql_fetch_array($count_result, MYSQL_ASSOC)){
             return false;
             }else{
             return $count_array[totalmsg];
             }
         }
    
     }

function get_count_new_section_messages($user_id, $section_id){
     # returns number of new messages
    $sql = "SELECT COUNT(messagetable.message_id) as totalmsg FROM messagetable, topictable, topicview 
	WHERE messagetable.topic_id = topictable.topic_id AND topictable.section_id =$section_id 
	AND topicview.user_id =$user_id AND messagetable.message_time > topicview.msg_date 
	AND topicview.topic_id = messagetable.topic_id";
    
     if(!$count_result = mysql_query($sql)){
         return false;
         }else{
         if(!$count_array = mysql_fetch_array($count_result, MYSQL_ASSOC)){
             return false;
             }else{
             return $count_array[totalmsg];
             }
         }
    
     }

function new_messages_in_section($user_id, $section_id){
     # returns true if new messages exist in the section
    # get all topics in section
    $sql = "SELECT topic_id FROM topictable WHERE section_id = $section_id";
    
     # echo "debug here!";
    if(!$section_result = mysql_query($sql)){
         return false; // not topics in section anyway
         }else{
        
         if($topic_array = mysql_fetch_array($section_result, MYSQL_ASSOC)){
             do{
                 if(new_messages_in_topic($topic_array[topic_id], $user_id)){
                     return true;
                     }
                
                 } while ($topic_array = mysql_fetch_array($section_result, MYSQL_ASSOC));
            
             }else{
             return false; // fucked
             }
        
         }
    
     }


function get_section_lastupdate($section_id){
     # returns date of most recent message in section
    $sql = "SELECT * FROM messagetable FROM messagetable, topictable, sectiontable
	WHERE topictable.section_id = sectiontable.section_id AND messagetable.topic_id = topictable.topic_id";
    
     }

# ## topic things
function get_topic_owner($topic_id){
     # input topic_id
    # returns array of username and user_id
    $sql = "SELECT sectiontable.user_id as owner_id, usertable.user_name as owner_name FROM topictable,sectiontable, usertable
        WHERE topictable.topic_id=$topic_id AND sectiontable.section_id=topictable.section_id AND usertable.user_id = sectiontable.user_id";
    
     if(!$query_result = mysql_query($sql)){
         return false;
         }else{
         if(!$owner_array = mysql_fetch_array($query_result, MYSQL_ASSOC)){
             return false;
             }else{
             return $owner_array;
             }
         }
    
    
     }


function get_count_topic_messages($topic_id){
     # returns number of messages in topic
    $sql = "SELECT COUNT(message_id) AS totalmsg FROM messagetable WHERE  topic_id = $topic_id";
    
     if(!$count_result = mysql_query($sql)){
         return false;
         }else{
         if(!$count_array = mysql_fetch_array($count_result, MYSQL_ASSOC)){
             return false;
             }else{
             return $count_array[totalmsg];
             }
         }
    
     }


function get_latest_message_id($topic_id){
     # returns the most recent message_id in a topic
    $sql = "SELECT message_id FROM messagetable WHERE topic_id=$topic_id ORDER BY message_id DESC LIMIT 1";
    
     if(!$query_result = mysql_query($sql)){
         return false;
         }else{
         if(!$result_array = mysql_fetch_array($query_result, MYSQL_ASSOC)){
             return false;
             }else{
             return $result_array[message_id];
             }
         }
    
     }

function can_user_add_message($user_array, $topic_array){
    
     // if not read only return true else ...
    if($topic_array[topic_readonly] == 'y'){
         $section_array = get_section($topic_array[section_id]);
         if(($user_array[user_sysop] == 'y') OR ($user_array[user_id] == $section_array[user_id])){
             return true;
             }else{
             return false;
             }
        
         }else{
         return true;
         }
     }

function can_user_edit_topic($user_array, $topic_array){
    
     # section owner or sysop
    # sysop
    if($user_array[user_sysop] == 'y')
         return true;
    
     $section_array = get_section($topic_array[section_id]);
    
     # section owner
    if($user_array[user_id] == $section_array[user_id])
         return true;
    
     return false;
     }

function can_user_edit_section($user_array, $section_array){
    
     # section owner, parent owner, sysop
    # sysop
    if($user_array[user_sysop] == 'y')
         return true;
    
     # section owner
    if($user_array[user_id] == $section_array[user_id])
         return true;
    
     # owner of parent section
    if($section_array[parent_id]){
         $parent_section_array = get_section($section_array[parent_id]);
         if($user_array[user_id] == $parent_section_array[user_id])
             return true;
         }
    
     return false;
     }


function inc_total_edits($user_array){
    
    
     $num_edits = $user_array["user_totaledits"] + 1;
    
     $sql = "UPDATE usertable SET user_totaledits=$num_edits WHERE user_id=$user_array[user_id]";
    
     if(!mysql_query($sql)){
         return false;
         }else{
         return true;
         }
     }


function get_userlist_array(){
     # returns number of messages in section
    $userlist_array = array();
    
     $sql = "SELECT user_id, user_name FROM usertable ORDER BY user_name";
    
    
     if(!$sql_result = mysql_query($sql)){
         return false;
         }else{
         if($current_array = mysql_fetch_array($sql_result))
             do{
             array_push($userlist_array, $current_array);
             } while ($current_array = mysql_fetch_array($sql_result));
         return $userlist_array;
         }
    
     }


function get_sectionlist_array($user_array){
     # returns list of sections the user can moderate
    $sectionlist_array = array();
    
     if($user_array[user_sysop] == 'n'){
         $sql = "SELECT section_id, section_title FROM sectiontable WHERE user_id = $user_array[user_id] ORDER BY section_title";
         }else{
         $sql = "SELECT section_id, section_title FROM sectiontable ORDER BY section_title";
         }
    
     if(!$sql_result = mysql_query($sql)){
         return false;
         }else{
         if($current_array = mysql_fetch_array($sql_result)){
             do{
                 array_push($sectionlist_array, $current_array);
                 } while ($current_array = mysql_fetch_array($sql_result));
             return $sectionlist_array;
             }else{
             return false;
             }
         }
    
     }

function get_users_online($self_id, $include_self){
     # returns list of users currently online
    $users_online_array = array();
    
     if(!$include_self){
         $sql = "SELECT whoison.user_id, usertable.user_popname, usertable.user_location, MINUTE(now() - whoison.timeon) as minutes, SECOND(now() - whoison.timeon) as seconds, user_name from whoison, usertable WHERE timeon > date_sub(now(), INTERVAL 5 minute) and whoison.user_id = usertable.user_id and usertable.user_status='Online' and usertable.user_id <> $self_id ORDER BY timeon DESC";
         }else{
         $sql = "SELECT whoison.user_id, usertable.user_popname, usertable.user_location, MINUTE(now() - whoison.timeon) as minutes, SECOND(now() - whoison.timeon) as seconds, user_name from whoison, usertable WHERE timeon > date_sub(now(), INTERVAL 5 minute) and whoison.user_id = usertable.user_id and usertable.user_status='Online' ORDER BY timeon DESC";
         }
    
     if(!$sql_result = mysql_query($sql)){
         return false;
         }else{
         if($current_array = mysql_fetch_array($sql_result)){
             do{
                 array_push($users_online_array, $current_array);
                 } while ($current_array = mysql_fetch_array($sql_result));
             # echo "debug: here";
            return $users_online_array;
             }else{
             return false;
             }
        
         }
    
     }

function update_last_time_on($user_id){
    
     $sql = "DELETE FROM whoison WHERE user_id=$user_id";
    
     if(!mysql_query($sql)){
         return false;
         }else{
         $sql = "INSERT INTO whoison (user_id) VALUES ('$user_id')";
        
         if(!mysql_query($sql)){
             return false;
             }else{
             return true;
            
             }
         }
     }

function get_instant_messages($user_id){
     # returns an array of instant message arrays
    $instant_message_array = array();
    
    
     $sql = "SELECT nexusmessage_id, text, from_id, user_name FROM nexusmessagetable, usertable WHERE nexusmessagetable.user_id=$user_id AND usertable.user_id = from_id ORDER BY nexusmessage_id DESC";
    
     if(!$sql_result = mysql_query($sql)){
         return false;
        
         }else{
         if($current_array = mysql_fetch_array($sql_result)){
             do{
                 array_push($instant_message_array, $current_array);
                 } while ($current_array = mysql_fetch_array($sql_result));
            
             return $instant_message_array;
             }else{
             return false;
             }
        
         }
    
     }

function mark_messages_read($user_id){
    
     $sql = "UPDATE nexusmessagetable SET readstatus='y' WHERE user_id = $user_id";
    
     if(!mysql_query($sql)){
         return false;
         }else{
         return true;
         }
     }

function get_count_unread_messages($user_id){
    
     $sql = "SELECT count(nexusmessage_id) AS total_msg FROM nexusmessagetable WHERE readstatus IS NULL AND user_id=$user_id";
    
     if(!$query_result = mysql_query($sql)){
         return false;
         }else{
         if(!$result_array = mysql_fetch_array($query_result, MYSQL_ASSOC)){
             return false;
             }else{
             return $result_array[total_msg];
             }
         }
    
     }


function delete_instant_messages($user_id){
    
     $sql = "DELETE FROM nexusmessagetable WHERE user_id=$user_id AND readstatus='y'";
    
     if(!mysql_query($sql)){
         return false;
         }else{
         return true;
         }
     }

function delete_previous_topicview($user_id){
    
     # removes any previous topic view data keeping unsubscribe info
    $sql = "DELETE FROM topicview WHERE user_id=$user_id AND unsubscribe <> '1'";
    
     if(!mysql_query($sql)){
         return false;
         }else{
         return true;
         }
     }

function get_latest_post_dates(){
     # returns an array of instant message arrays
    $latest_post_array = array();
    
    
     $sql = "SELECT topic_id, max(message_time) as message_time FROM messagetable GROUP BY topic_id";
    
     if(!$sql_result = mysql_query($sql)){
         return false;
        
         }else{
         if($current_array = mysql_fetch_array($sql_result)){
             do{
                 array_push($latest_post_array, $current_array);
                 } while ($current_array = mysql_fetch_array($sql_result));
            
             return $latest_post_array;
             }else{
             return false;
             }
        
         }
    
     }

function get_user_array_from_name($username){
    /**
     * takes user_id and returns an array of their userinfo
     * INPUT user_id
     * OUTPUT assoc array of user_info or false if user is not found
     */
    
     // get array
    $sql = "SELECT * FROM usertable WHERE user_name = '$username'";
     # echo "debug $sql<br>";
    if(!$user_info = mysql_query($sql)){
         return false;
         }
    
     if(mysql_num_rows($user_info)){
        
         $user_array = mysql_fetch_array($user_info, MYSQL_ASSOC);
         return $user_array;
        
         }else{
         return false;
         }
    
     }

function get_last_time_on($user_id){
    
     $sql = "SELECT whoison_id FROM whoison WHERE user_id = $user_id";
    
     if(!$lasttimeon = mysql_query($sql)){
         return false;
         }
    
     if ($num = mysql_num_rows($lasttimeon)){
         // they have been on and looked at something
        $sql = "SELECT DATE_FORMAT(timeon,\"%a %b %D - %H:%i %Y\") as formatted_time FROM whoison WHERE user_id = $user_id";
        
         if(!$timeinfo = mysql_query($sql)){
             return false;
             }
        
         $result_array = mysql_fetch_array($timeinfo, MYSQL_ASSOC);
        
         $lasttimeon = $result_array[formatted_time];
         }else{
         return false;
         }
    
     return $lasttimeon;
     }

function get_last_edited_topic($user_id){
    
     $sql = "SELECT topic_title, messagetable.topic_id  FROM messagetable, topictable where user_id=$user_id and messagetable.topic_id=topictable.topic_id order by message_id desc limit 1";
    
     if(!$lastedit = mysql_query($sql)){
         return false;
         }
    
     $result_array = mysql_fetch_array($lastedit, MYSQL_ASSOC);
    
     return $result_array;
    
     }
# ### scoreboard functions
function get_top_posters_array(){
    
     $sql = 'select count(message_id) as total , user_name, usertable.user_id from messagetable, usertable where message_time >  date_sub(now(), INTERVAL 30 day) and usertable.user_id = messagetable.user_id group by messagetable.user_id order by total desc limit 10';
    
     $top_posters_array = array();
    
    
     if(!$sql_result = mysql_query($sql)){
         return false;
        
         }else{
         if($current_array = mysql_fetch_array($sql_result)){
             do{
                 array_push($top_posters_array, $current_array);
                 } while ($current_array = mysql_fetch_array($sql_result));
            
             return $top_posters_array;
             }else{
             return false;
             }
        
         }
    
    
     }


function get_top_moderator_array(){
    
     $sql = 'select user_name, count(message_id) as total, usertable.user_id  from messagetable, topictable, sectiontable, usertable  where message_time >  date_sub(now(), INTERVAL 30 day) and topictable.topic_id = messagetable.topic_id and topictable.section_id = sectiontable.section_id and usertable.user_id = sectiontable.user_id group by sectiontable.user_id order by total desc limit 10';
    
     $top_posters_array = array();
    
    
     if(!$sql_result = mysql_query($sql)){
         return false;
        
         }else{
         if($current_array = mysql_fetch_array($sql_result)){
             do{
                 array_push($top_posters_array, $current_array);
                 } while ($current_array = mysql_fetch_array($sql_result));
            
             return $top_posters_array;
             }else{
             return false;
             }
        
         }
    
    
     }


function get_top_section_array(){
    
     $sql = 'select section_title, count(message_id) as total, sectiontable.section_id  from messagetable, topictable, sectiontable  where message_time >  date_sub(now(), INTERVAL 30 day) and topictable.topic_id = messagetable.topic_id and sectiontable.section_id = topictable.section_id group by sectiontable.section_id order by total desc limit 10';
    
     $top_posters_array = array();
    
    
     if(!$sql_result = mysql_query($sql)){
         return false;
        
         }else{
         if($current_array = mysql_fetch_array($sql_result)){
             do{
                 array_push($top_posters_array, $current_array);
                 } while ($current_array = mysql_fetch_array($sql_result));
            
             return $top_posters_array;
             }else{
             return false;
             }
        
         }
    
    
     }


function get_top_topic_array(){
    
     $sql = 'select topic_title, count(message_id)  as total, topictable.topic_id from messagetable, topictable  where message_time >  date_sub(now(), INTERVAL 30 day) and topictable.topic_id = messagetable.topic_id group by messagetable.topic_id order by total desc limit 10';
    
     $top_posters_array = array();
    
    
     if(!$sql_result = mysql_query($sql)){
         return false;
        
         }else{
         if($current_array = mysql_fetch_array($sql_result)){
             do{
                 array_push($top_posters_array, $current_array);
                 } while ($current_array = mysql_fetch_array($sql_result));
            
             return $top_posters_array;
             }else{
             return false;
             }
        
         }
    
    
     }
# ## end scoreboard functions
# ### sanity
function escape_input($untrusted_string)
{
     return nl2br(htmlspecialchars($untrusted_string, ENT_NOQUOTES));
     # return  mysql_escape_string(nl2br($untrusted_string));
}
# ####
?>
