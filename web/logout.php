<?php
//logs the user out, sets their location to Logout
// last update May 22 2002

include('../includes/theme.php');
include('../includes/database.php');


$db = opendata();
session_start();
$sql = 'UPDATE usertable SET user_status="Offline" WHERE user_id='.$current_id;
if(!mysql_query($sql)){
 nexus_error();
}

#session_destroy();
header("Location: http://".$_SERVER['HTTP_HOST']."/");
exit;

?>






