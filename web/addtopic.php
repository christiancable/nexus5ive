<?php

include('../includes/theme.php');
include('../includes/database.php');



# to do
# get user from session
# open connection with user privs

#$topicid=1;

$db = opendata();

if(isset($parent_id)){
} else {
$parent_id=NULL;
}

if(isset($submit)){
  htmlheader("Returning...","section.php?section=$section_id",1);
  pagetitle("Returning...");
  $userid=$current_id;

  // never trust the client ...

  $sql = "SELECT user_id FROM  sectiontable WHERE section_id=$section_id ";
 # echo $sql;

  $ownerinfo = mysql_query($sql);
  if (mysql_num_rows($ownerinfo))
  {
   $ownerrow = mysql_fetch_array($ownerinfo);
    if(!is_section_owner($section_id,$userid,$db)){
     echo"<b1>You are not the section owner</h1>";
     exit() ;
    }
  } else {
    echo"<b1>No such section</h1>";
    exit();
  }




  if(isset($usehtml)){
      $title = nl2br($title);
      $text  = nl2br($text);
  // use html
  } else {
  // strip out html replace line endings
   $title = htmlspecialchars($title, ENT_QUOTES);
   $text = htmlspecialchars($text, ENT_QUOTES);
   $title = nl2br($title);
   $text = nl2br($text);
  }

  $sql = "INSERT topictable(topic_title, topic_desctiption,section_id)VALUES('$title','$text','$section_id')";
 # echo $sql;
  # should really check somethings here : _ )
 # echo $sql;
  mysql_query($sql);
  echo "<a href=\"section.php?section=$section_id\"> Return </a>";
} else {

# default subject should be "re".$parent_id->message_title

htmlheader("Create Topic ",NULL,1);
pagetitle("Create Topic");
$userid=$current_id;

#$sql = "SELECT * FROM topictable WHERE topic_id=$topic_id";
#$topicinfo = mysql_query($sql);
#$topicrow = mysql_fetch_array($topicinfo);

?>


<form method="post" action="<? echo $PHP_SELF?>">
<b>Title:</b><input type="Text" name="title" ><br><br>
<b>Topic Info:</b><br><textarea name="text" rows="10" cols="80"></textarea><br>
<?php

   echo "<input type=\"checkbox\" name=\"usehtml\"> Use HTML";

?>
<input name="section_id" type=hidden value="<?php echo $section_id?>"><br><br>
<input type="Submit" name="submit" value="Create Topic">
<a href="section.php?section=<?php echo $section_id?>">[ Cancel ]</a>


</form>

<?php

}

htmlfooter();
?>
