<?php

include('../includes/database.php');


$db = opendata();
session_start();

setuser_offline($_SESSION[current_id]);

session_destroy();

header("Location: http://".$_SERVER['HTTP_HOST']."/");
exit;
?>






