<?php
include('../includes/theme.php');
include('../includes/database.php');
$start_time = time();

$db = opendata();

    htmlheader("Main Menu",NULL,1);
    pagetitle("Main Menu");
//    echo "Please choose a section<br><br>";
    $sql = "SELECT section_id, section_title FROM sectiontable WHERE parent_id = 1";
    $sectioninfo = mysql_query($sql, $db);          
	if(mysql_num_rows($sectioninfo) ) 
	{
		# table begin
		$count = 0;
		?>
		<TABLE width="100%" border="0">
		<?php
		if ($myrow = mysql_fetch_array($sectioninfo))
	        do {
	          if($count % 2 == 0 ) 
				  {
					echo "<TR VALIGN=TOP>";
					}
			
				echo "<TD>";
				sectionheader($myrow);

				
				#}
				
				
				echo "</b></font></TD>";
				$count ++;
				
	          } while ($myrow = mysql_fetch_array($sectioninfo));
			 
			  if($count % 2){
			   echo "<TD></TD>";
			  }
			  # table end
			  # check if we need a filler cell
			  ?>
			  </TABLE>
			  <?php
	   } else {
       printf("<br><b> no sections yet ! </b><br>");
	   }
   
   drawline();
   echo "<a href=\"leap.php\">[ Topic Leap ]</a> -- ";
   echo "<a href=\"myinfo.php\">[ Examine User ]</a> -- "; 
   echo "<a href=\"userson.php\">[ Users On ]</a> -- "; 
   echo "<a href=\"messages.php\">[ Messages ]<a/>";
$end_time = time();
echo "<!-- Time taken was ".($end_time - $start_time)." seconds -->";
htmlfooter();
?>






