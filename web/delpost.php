<?php

include('../includes/theme.php');
include('../includes/database.php');



# to do
# get user from session
# open connection with user privs

$db = opendata();

if(isset($submit)){
  
  htmlheader("Returning...","readtopic.php?section_id=".$section_id."&topic_id=".$topic_id,1);
  pagetitle("Returning...");
  $userid=$current_id;

  if(isset($confirm) && is_message_owner($message_id,$userid,$db))
  {
	$sql="DELETE FROM messagetable WHERE message_id=$message_id";
	echo "Deleted ".mysql_query($sql, $db)." messages<br>";
    echo "<br><a href=\"readtopic.php?section_id=$section_id&topic_id=$topic_id\"> Return </a>";
 }
} else {

// not submit
        htmlheader("Remove Message",NULL,1);
        pagetitle("Remove Message");

        ?>

        <form method="post" action="<? echo $PHP_SELF?>">
        <?php
        $sql = "SELECT * FROM messagetable WHERE message_id=".$message_id;
        $messageresult = mysql_query($sql,$db);
        $messagerow= mysql_fetch_array($messageresult);

                $sql = "SELECT section_id FROM topictable WHERE topic_id=$topic_id ";
        $topicinfo = mysql_query($sql, $db);
                $topicrow = mysql_fetch_row($topicinfo);

                #echo "$sql debug: $messagerow[0]";
        displaymessage($messagerow,$topic_id,$db,NULL);
        drawline();
        ?>


        <input type="hidden" name="topic_id" value="<?php echo $topic_id ?>">
                <input name="section_id" type=hidden value="<?php echo $topicrow[0]?>">

        <input type="hidden" name="message_id" value="<?php echo $message_id ?>">
        Delete the above message ?
        <input type="Checkbox" name="confirm" vaule="yes"><br><br>
        <input type="Submit" name="submit" value="Okay">
        </form>


<?php
}




htmlfooter();
?>