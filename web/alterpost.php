<?php

include('../includes/theme.php');
include('../includes/database.php');
$db = opendata();


if(isset($submit)){
#  htmlheader("Returning...",NULL,1);
   htmlheader("Returning...","readtopic.php?section_id=".$section_id."&topic_id=".$topic_id,1);
   pagetitle("Returning...");
   $userid=$current_id;

   if(isset($usehtml)){
      $comment = nl2br($comment);
      // use html
   } else {
     // strip out html replace line endings
      $tempmessage = htmlspecialchars($comment, ENT_QUOTES);
      $comment = nl2br($tempmessage);
   }
  $sql = "SELECT * FROM usertable WHERE user_id=$userid";
  $result=mysql_query($sql);
  $resultarray=mysql_fetch_array($result);


  $sql = "UPDATE messagetable SET message_title='$subject', message_text='$comment' WHERE message_id=$message_id";

  if (is_message_owner($message_id,$userid,$db) ){
      $sql = "UPDATE messagetable SET message_title='$subject', message_text='$comment' WHERE message_id=$message_id";
      mysql_query($sql);
  } else {
    echo "<h2>Not Section Owner</h2>";
  }
  echo "<a href=\"readtopic.php?section_id=$section_id&topic_id=$topic_id\"> Return </a>";

} else {

  $sql = "SELECT * FROM topictable WHERE topic_id=$topic_id";
  $topicinfo = mysql_query($sql);
  $topicrow = mysql_fetch_array($topicinfo);

  $sql = "SELECT * FROM messagetable WHERE message_id=$message_id";
  #echo $sql;
  $messageinfo = mysql_query($sql);
  $messagerow = mysql_fetch_array($messageinfo);

  htmlheader("Edit Message : ".$topicrow["topic_title"],NULL,1);
  pagetitle("Edit Message : ".$topicrow["topic_title"]);
  $userid=$current_id;

  ?>
  <form method="post" action="<? echo $PHP_SELF?>">
  <?php

  $sql = "SELECT user_name FROM usertable WHERE user_id=".$messagerow["user_id"];
  #echo $sql;
  $ownerinfo = mysql_query($sql);
  $ownerrow = mysql_fetch_array($ownerinfo);

  echo "<b>User:</b> ".$ownerrow[0]."<br>";
  ?>
  <b>Subject: </b><input type="Text" name="subject" value="
  <?php
  echo $messagerow["message_title"]."\"";


  $sql = "SELECT user_id FROM sectiontable, topictable WHERE topictable.topic_id=$topic_id AND topictable.section_id=sectiontable.section_id;";
  $ownerinfo = mysql_query($sql);
  $ownerrow = mysql_fetch_array($ownerinfo);
  // current section owner is now in owerrow[1]
  $messagetext = ereg_replace("<br />","",$messagerow["message_text"]);
  ?>
  ><br>
  <br>
  <b>Comment:</b><br><textarea name="comment" rows="10" cols="80"><?php echo $messagetext?></textarea><br>
  <input name="topic_id" type=hidden value="<?php echo $topic_id?>">
  <input name="message_id" type=hidden value="<?php echo $message_id?>">
  <input name="section_id" type=hidden value="<?php echo $topicrow[2]?>">
  <input type="checkbox" name="usehtml"> Use HTML<br><br>

  <input type="Submit" name="submit" value="Update Comment">
  <a href="readtopic.php?section_id=<?php echo $topicrow[2]?>&topic_id=<?php echo $topic_id?>">[ Cancel ]</a>
  </form>

  <?php


}

htmlfooter();
?>
