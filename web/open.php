<?php
include('../includes/theme.php');
include('../includes/database.php');


$db = opendata();


if(isset($submit)){

 $sql="SELECT * FROM usertable WHERE user_name=\"$username\"";
 $logininfo=mysql_query($sql,$db);
 $loginresult=mysql_fetch_array($logininfo);
 
  if($loginresult){
	if($loginresult["user_password"]==$password)
	 {
		    global $current_id;
		    $current_id = $loginresult["user_id"];
		    session_register("current_id");
			session_register("my_theme");
			$current_id = $loginresult["user_id"];
			$my_theme = $loginresult["user_theme"];
			htmlheader("Nexus","mainmenu.php",1);
			pagetitle("Welcome to Nexus");
			// increase number of times on nexus here
			$num_of_visits = $loginresult["user_totalvisits"]+1;
			$sql = "UPDATE usertable SET user_totalvisits=".$num_of_visits." WHERE user_id=$current_id";
			if(!$nextsection = mysql_query($sql)){
				nexus_error();
			}
            //set status logged in
            $sql = 'UPDATE usertable SET user_status="Online" WHERE user_id='.$current_id;
            if(!mysql_query($sql)){
				nexus_error();
			}

			//////
	    } else {
		   htmlheader("uh oh", $PHP_SELF,0);
		   pagetitle("uh oh");
		   echo "<b>Error:</b> please check username and password";// no user found
	    }
  } else {
  // no user found
	    htmlheader("uh oh", $PHP_SELF,0);
	    pagetitle("uh oh");
	    echo "<b>Error:</b> please check username and password";// no user found
  }
} else {

htmlheader("Login",NULL,0);
pagetitle("Login");
include('logo.html');
?>



<form method="post" action="<? echo $PHP_SELF?>">
Username:<br><input type="Text" name="username"><br>
Password:<br><input type="password" name="password"><br>
<input type="Submit" name="submit" value="Login">
</form>
<?php
}

htmlfooter();
?>

