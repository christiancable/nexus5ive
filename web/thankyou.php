<?php
# send sysop some email
# christian - nov 2002


mail( "sysop@nexus5.org.uk", "Nexus Account Request",
	"New account time\n\nUser Name is $username\nReal Name is $realname\nPassword is $password\nEmail is $email", "From: $email" );

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>Thank You!</title>
</head>
<body bgcolor="#3a75af" text="#ffffff" link="#ffff33" vlink="#00ff00">
<div align="left">
  <h1><font color="#33ff00" face="Verdana, Arial, Helvetica, sans-serif">Thank 
    you!</font></h1>
  <p><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif">You should 
    recieve an email as soon as your account is ready ,hopefully not more than 
    a few hours. If you have any questions while you're waiting or even if you're 
    just a bit bored free free to pop me an <a href="mailto:sysop@nexus5.org.uk">email</a>. 
    </font></p>
  <p><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif">Christian 
    (aka fraggle) </font></p>
  <pre><font face="Verdana, Arial, Helvetica, sans-serif"><br>
</font></pre>
</div>
</body>
</html>

