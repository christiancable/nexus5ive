<?php

include('../includes/theme.php');
include('../includes/database.php');

$db = opendata();
session_start();
$user_id = $_SESSION[current_id];



if(isset($submit)){

	$message_id = $HTTP_POST_VARS[message_id];
	$topic_id = $HTTP_POST_VARS[topic_id];  
	$section_id = $HTTP_POST_VARS[section_id];
	
	if(isset($confirm) && is_message_owner($message_id,$user_id,$db))
	{
		if(delete_message($message_id))
		{
			# deleted message		
		} else {
			# delete has failed, what should happen here then?
		}
	} else {
		# user has not selected to delete the message so do nothing
	}
 
	# in all cases return to section here
	header("Location: http://".$_SERVER['HTTP_HOST']."/readtopic.php?section=$section_id&topic_id=$topic_id");
	exit();
 
 
} else {

// not submit
        htmlheader("Remove Message",NULL,1);
        pagetitle("Remove Message");

        ?>

        <form method="post" action="<? echo $PHP_SELF?>">
        <?php
	
	$message_array = get_message($message_id);
	$topic_array = get_topic($topic_id);

               
	displaymessage($message_array,$topic_id,$db,NULL);
        drawline();
        ?>


        <input type="hidden" name="topic_id" value="<?php echo $topic_id ?>">
        <input name="section_id" type=hidden value="<?php echo $topic_array[section_id]?>">

        <input type="hidden" name="message_id" value="<?php echo $message_id ?>">
        Delete the above message ?
        <input type="Checkbox" name="confirm" vaule="yes"><br><br>
        <input type="Submit" name="submit" value="Okay">
        </form>


<?php
}




htmlfooter();
?>
