<?php

include('../includes/theme.php');
include('../includes/database.php');



# to do
# get user from session
# open connection with user privs

$db = opendata();

if(isset($submit)){

  $sql="DELETE FROM messagetable WHERE topic_id=$topic_id";
 // $childinfo = mysql_query($sql, $db);
  htmlheader("Returning...","section.php?section=$section_id",1);
 // htmlheader("Returning...",NULL,1);
  pagetitle("Returning...");

  $userid=$current_id;

   if( isset($confirm) && is_section_owner($section_id,$userid,$db)      ) {
      $deletemsginfo = mysql_query($sql,$db);
      echo "Deleted ".mysql_affected_rows($db)." messages<br>";
      $sql = "DELETE FROM topictable WHERE topic_id=$topic_id";
      $deletetopicinfo = mysql_query($sql,$db);
      echo "Deleted ".mysql_affected_rows($db)." topics<br>";
   }


  echo "<br><a href=\"section.php?section=$section_id\"> Return </a>";

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

        displaytopic($topicrow,$db,$current_id);

        drawline();
        ?>


        <input type="hidden" name="topic_id" value="<?php echo $topic_id ?>">
                <input type="hidden" name="section_id" value="<?php echo $topicrow[2]?>">

        Delete the above topic ?
        <input type="Checkbox" name="confirm" vaule="yes"><br><br>
        <input type="Submit" name="submit" value="Okay">
        </form>


<?php
}




htmlfooter();
?>