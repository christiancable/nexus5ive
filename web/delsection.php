<?php

include('../includes/theme.php');
include('../includes/database.php');



# to do
# get user from session
# open connection with user privs

$db = opendata();

if(isset($submit)){

  $sql="SELECT * from messagetable WHERE topic_id=$topic_id";
  $topicinfo = mysql_query($sql, $db);
  htmlheader("Returning...","section.php?section_id=".$section_id,1);
  pagetitle("Returning...");
  $userid=$current_id;

  if(isset($confirm))
  {
  if(mysql_num_rows($topicinfo)){
    // for each child
     $sql = "SELECT parent_id FROM messagetable WHERE message_id=$message_id";
     echo $
#     $parentinfo = mysql_query($sql, $db);
#     $parentrow = mysql_fetch_array($parentinfo);
#     echo "Parent should be $parentrow[0]";

  }
  $sql="DELETE FROM messagetable WHERE message_id=$message_id";
  echo "Deleted ".mysql_query($sql, $db)." messages<br>";

  echo "<br><a href=\"section.php?section_id=$section_id\"> Return </a>";
 }
} else {
// not submit
        htmlheader("Remove Topic",NULL,1);
        pagetitle("Remove Topic");

        ?>

        <form method="post" action="<? echo $PHP_SELF?>">
        <?php
        $sql = "SELECT * FROM topictable WHERE topic_id=".$topic_id;
        $topicresult = mysql_query($sql,$db);
        $topicrow=mysql_fetch_array($topicresult);
        #echo "$sql debug: $messagerow[0]";
#        displaymessage($row,NULL,$db,NULL);
        drawline();
        ?>


        <input type="hidden" name="topic_id" value="<?php echo $topic_id ?>">
        <input type="hidden" name="section_id" value="<?php echo $section_id?>">
        Delete the above topic ?
        <input type="Checkbox" name="confirm" vaule="yes"><br><br>
        <input type="Submit" name="submit" value="Okay">
        </form>


<?php
}




htmlfooter();
?>
