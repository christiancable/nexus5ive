<?php
include('../includes/theme.php');
include('../includes/database.php');
$db = opendata();

if(isset($submit)){

   // where to return to
   if (isset($section_id)) {
        htmlheader("Updating User...","section.php?section=$section_id",1);
   } else htmlheader("Updating User...","section.php?section=1",1);

   // check privs here

   // get current info
   $sql = "SELECT * FROM usertable WHERE user_id=".$lookat;
   $myinfo = mysql_query( $sql,$db);
   $myrow = mysql_fetch_array($myinfo);

   // tell the user what is happenig
   pagetitle("Updating: ".$myrow[1]);

   // strip out html from form

   $email = htmlspecialchars($email, ENT_QUOTES);
   $email = nl2br($email);

   $popname = htmlspecialchars($popname, ENT_QUOTES);
   $popname = nl2br($popname);

   $comment = htmlspecialchars($comment, ENT_QUOTES);
   $comment = nl2br($comment);

   if(isset($display_backwards)){
      $backwards="y";
    } else {
      $backwards="n";
    }
   $sql = "UPDATE usertable SET user_email='$email', user_popname='$popname', user_comment='$comment' ,user_realname='$realname',user_display='$display', user_backwards='$backwards' WHERE user_id=$lookat";
   if(is_user_owner($current_id,$lookat)){
      Mysql_query($sql);
   }
   htmlfooter();
   exit();
} // end update user


if(isset($lookat)){
   // get requested user info

   $sql = "SELECT * FROM usertable WHERE user_id=".$lookat;
   $myinfo = mysql_query( $sql,$db);
   $myrow = mysql_fetch_array($myinfo);


   // does that user exist ?
   if(mysql_num_rows($myinfo)){
      htmlheader("Examining User ".$myrow[1],NULL,1);
   } else {
     htmlheader("No Such User","section.php?section=1",1);
     end();
   }
   pagetitle("User Info: ".$myrow[1]);

 
   /* 
	   update total edits if not already done
	   cheat here to update the total number of edits without me having to do it 
	   only needed because I'm only just interested in this value
   */
   
   if($myrow["user_totaledits"]=="0")
	{
	   $sql="SELECT count(message_id) FROM messagetable WHERE user_id=".$myrow["user_id"];
		if(!$sqlresult =  mysql_query($sql, $db)){
			nexus_error();
		}
		$sqlrow = mysql_fetch_array($sqlresult);
		// update user_totaledits
		$sql="UPDATE usertable SET user_totaledits=".$sqlrow[0]." WHERE user_id=".$myrow["user_id"];
		//echo "debug: $sql<br>does this break here then hmmmmm";
		if(!mysql_query($sql, $db)){
			nexus_error();
		}


	}
   
   if($current_id==$myrow[0]) {
   // edit details
    ?>
    <form method="post" action="<? echo $PHP_SELF?>">
    <b>Username:</b> <?php echo $myrow[1] ?><br>
    <b>Real Name :</b><input type="Text" size=50 maxlength=50 name="realname"
    <?php echo "value=\"$myrow[6]\"><br>";?>
    <b>Popname   :</b><input type="Text" size="50" maxlength=50 name="popname"
    <?php echo "value=\"$myrow[4]\"><br>"; ?>
    <b>Email     :</b><input type="Text" size="50" maxlength=50 name="email"
    <?php echo "value=\"$myrow[2]\"><br>";?>
    <?php
    drawline()
    ?>
    <b>Options:</b><br> Show only the last <select name="display">
    <?php
	if ($myrow[8] == 255) {
	?>
		<option value="<?php echo $myrow[8]?>">All</option>
	<?php
	} else {
	?>
		<option value="<?php echo $myrow[8]?>"><?php echo $myrow[8]?></option>
	<?php
	}
	
	?>
    <option value="5">5</option>
    <option value="10">10</option>
    <option value="15">15</option>
    <option value="20">20</option>
    <option value="25">25</option>
    <option value="30">30</option>
    <option value="40">40</option>
    <option value="50">50</option>
    <option value="100">100</option>
    <option value="150">150</option>
    <option value="255">All</option>
    </select> messages<br>
    Use freakybackwards mode<input type="checkbox" name="display_backwards"
    <?php
    if ($myrow[9]=="y")
         echo "CHECKED ";
    ?>
    >
	<br>
	<a href="catchup.php">[ Catch up with all topics ]</a>
    <?php
    drawline()
    ?>
    <?php
	echo "<b>Wisdom of ".$myrow["user_name"].":</b><br>";
    $infotext = ereg_replace("<br />","",$myrow[3]);
    ?>
    <textarea name="comment" rows=5 cols=80><?php echo $infotext ?></textarea>
    <input type="Submit" name="submit" value="Update Details">
    <input type="hidden" name="lookat" value="<?php echo $lookat ?>">
    <?php
    if(isset($section_id)){
      ?> <input type="hidden" name="section_id" value="<?php echo $section_id?>"><?php
    }
	drawline();
    echo "<b>Usage Info:</b><br>";
	echo "Number of Posts: ".$myrow["user_totaledits"]."<br>";
//	echo "Total Visits to Nexus: ".$myrow["user_totalvisits"]."<br>";
    if($myrow["user_totalvisits"])
	  echo "Current Nexus Score: <b>".get_score($myrow["user_id"])."</b>";
	drawline();
    ?>
    </form>
    <?php


   } else { // end edit details
   // view details
      echo "<b>Real Name:</b> ".$myrow[6]."<br>";
      echo "<b>Popname :</b> ".$myrow[4]."<br>";
      echo "<b>Email   :</b> ";
      echo "<a href=\"mailto:$myrow[2]\">$myrow[2]</a><br>";
     if(isset($section_id)){
      ?> <input type="hidden" name="section_id" value="<?php echo $section_id?>"><?php
      }
      echo "<br><b>The Wisdom of ".$myrow["user_name"].":</b><br>";
      drawline();
      echo $myrow[3]."<br>";
	  # get details from whoison
      $sql = "SELECT whoison_id FROM whoison WHERE user_id = $lookat";
      $lasttimeon = mysql_query($sql, $db);
      if ($num = mysql_num_rows($lasttimeon)) {
           // they have been on and looked at something
           $sql = "SELECT DATE_FORMAT(timeon,\"%a %b %D - %H:%i %Y\") FROM whoison WHERE user_id = $lookat";
           $timeinfo = mysql_query($sql, $db);
           $timerow = mysql_fetch_row($timeinfo);
           $lasttimeon = $timerow[0];
      } else {
           $lasttimeon = "Never!";
      }
      drawline();
	  echo "<b>Usage Info:</b></br>";
	  drawline();
	  echo "Number of Posts: ".$myrow["user_totaledits"]."<br>";
	 // echo "Total Visits to Nexus: ".$myrow["user_totalvisits"]."</br>";
          echo "Last Visit on: ".$lasttimeon."<br>";
	  if($myrow["user_totalvisits"])
		  echo "Current Nexus Score: <b>".get_score($myrow["user_id"])."</b>";
	  drawline();
   } // end view details

   // common details stuff
   if (isset($section_id)) {
        echo "<br><a href=\"section.php?section=$section_id\">[ return to section ]</a>";
   } else {
      echo "<br><a href=\"section.php?section=1\">[ return to main menu ]</a>";
   }


} else {   // end isset($lookat)
// choose user
   htmlheader("Examine User",NULL,1);
   pagetitle("Examine which user ... ");
   $sql = "SELECT * FROM usertable ORDER BY user_name";
   $usersinfo = mysql_query( $sql,$db);
   echo "<form method=\"post\" action=\"$PHP_SELF\">";
   echo "<select name=\"lookat\">";
   if( $usersrow = mysql_fetch_array($usersinfo) )
        do {
            echo "<option value=\"$usersrow[0]\">$usersrow[1]</option>";
            }
        while ( $usersrow = mysql_fetch_array($usersinfo) );
   echo "</select><br><br><input type=\"Submit\" name=\"Okay\" value=\"Okay\">";
   echo "</form>";

} // end choose user


htmlfooter();

?>
