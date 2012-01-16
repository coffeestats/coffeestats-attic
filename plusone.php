<?php
include('auth/lock.php');
include("header.php"); 
?>
<b>We heard you made coffee, right?</b><br/><br/>
<center>
<form action="" method="post">
<input class="imadecoffee" type="submit" value="Uuh yeah. And It tastes awesome"/><br />
</form></center>

<?php 
if($_SERVER["REQUEST_METHOD"] == "POST") 
{
include('auth/config.php');
$sql="INSERT INTO cs_coffees VALUES ('','".$_SESSION['login_id']."', NOW() ); ";
$result=mysql_query($sql);
echo("<br/><center>Your coffee was registered</center>");
}
?>

<?php
	include('footer.php');
?>

