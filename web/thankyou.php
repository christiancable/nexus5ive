<?php
# DELETE ME? - cfc - 07/02/2014
# 
# send sysop some email
# updated the colour to the housestyle - feb 15 2003
# christian 


mail("sysop@nexus5.org.uk", "Nexus Account Request", "Cut and Paste\n insert into usertable (user_name, user_realname, user_password, user_email ) values ('$username', '$realname', '$password', '$email');\n from $_SERVER[REMOTE_ADDR] ", "From: $email");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>Thank You!</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body bgcolor="#FFFFFF" text="#000000">
<div align="left">
  <h1><font color="#000099" face="Verdana, Arial, Helvetica, sans-serif">Thank 
    you!</font></h1>
  <table width="100%" border="0">
    <tr>
      <td bgcolor="#d6dff5">&nbsp;</td>
    </tr>
    <tr>
      <td><p><font face="Verdana, Arial, Helvetica, sans-serif">You should recieve 
          an email as soon as your account is ready ,hopefully not more than a 
          few hours. If you have any questions while you're waiting or even if 
          you're just a bit bored feel free to pop me an <a href="mailto:sysop@nexus5.org.uk">email</a>. 
          </font></p>
        <p><font face="Verdana, Arial, Helvetica, sans-serif">Christian (aka fraggle) 
          </font></p>
  </td>
    </tr>
    <tr>
      <td bgcolor="#d6dff5">&nbsp;</td>
    </tr>
  </table>
  <p>
<p><font face="Verdana, Arial, Helvetica, sans-serif"> </font></p>
  <pre><font face="Verdana, Arial, Helvetica, sans-serif"><br>
</font></pre>
</div>
</body>
</html>
