<?php

	include('../phplib/php/template.inc');

	$pagetitle = "Test";
	$background = "#3A75AF";
	$textcolour = "#ffffff";
	$linkcolour = "#FFFF33";
	$visitedlinkcolour = "#00FF00";
	$headerbackground = "#CCCCCC";
	$headercolour = "#000000";
	$fontface = "Verdana, Arial, Helvetica, sans-serif";
	
	$t = new Template("../templates/xp");

	$t->set_file("MyFileHandle", "pageheader.ihtml");

	$t->set_var('pagetitle',$pagetitle);
	$t->set_var("background",$background);
	$t->set_var("textcolour",$textcolour);
	$t->set_var("linkcolour",$linkcolour);
	$t->set_var("visitedlinkcolour",$visitedlinkcolour);
	$t->set_var("headerbackground",$headerbackground);
	$t->set_var("headercolour",$headercolour);
	$t->set_var("fontface",$fontface);



	$t->parse("MyOutput","MyFileHandle");
    
	$t->p("MyOutput");
	
?>