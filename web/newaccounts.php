<?php 
include('../includes/theme.php');
include('../includes/database.php');

$db = opendata();

htmlheader("New Accounts Policy",NULL,0)

?>
<h1><font color="#33FF00">New accounts policy</font></h1>
<pre>
Accounts are issued at the discretion of the sysop and the members of nexus.

But we pretty much like everyone!

all requests for membership should be mailed to the <a href="mailto:sysop@nexus5.org.uk">sysop</a>
with a choice of username and password.

with thanks

Christian
</pre>
<?php

htmlfooter();
?>