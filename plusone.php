<?php
include("header.php"); 
include('auth/lock.php');
?>
<center>
<form action="" method="post">
<input class="imadecoffee" type="submit" value="Mhh. Coffee. It tastes awesome"/><br />
</form></center>

<?php 
if($_SERVER["REQUEST_METHOD"] == "POST") 
{
include('auth/config.php');
$sql="INSERT INTO cs_coffees VALUES ('','".$_SESSION['login_id']."', NOW() ); ";
$result=mysql_query($sql);
echo("<center>Your coffee was registered</center>");
}
?>

<?php
	include('footer.php');
?>

