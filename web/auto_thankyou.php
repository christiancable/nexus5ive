<?php
# send sysop some email
# updated the colour to the housestyle - feb 15 2003
# christian 

include('../includes/theme.php');
include('../includes/database.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>Thank You!</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<body bgcolor="#FFFFFF" text="#000000">
<?
  if(autoadduser($username, $password, $realname, $email) == true) {
?>

<div align="left">
  <h1><font color="#000099" face="Verdana, Arial, Helvetica, sans-serif">Thank 
    you!</font></h1>
  <table width="100%" border="0">
    <tr>
      <td bgcolor="#d6dff5">&nbsp;</td>
    </tr>
    <tr>
      <td>
        <p><font face="Verdana, Arial, Helvetica, sans-serif"><? echo "$username"; ?>, your account is 
          now ready to use.</font></p>
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
<?
  }
  else {
?>
<div align="left">
  <h1><font color="#000099" face="Verdana, Arial, Helvetica, sans-serif">Sorry!</font></h1>
  <table width="100%" border="0">
    <tr>
      <td bgcolor="#d6dff5">&nbsp;</td>
    </tr>
    <tr>
      <td>
        <p><font face="Verdana, Arial, Helvetica, sans-serif">A problem has occured when you have tried to add your account<BR><BR>
        This may be due to the fact that this user name has already been taken or you have not entered a password.</font></p>
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

<?
  }
?>
</body>
</html>

