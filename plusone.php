<?php
include('auth/lock.php');
include("header.php"); 
?>
We heard you made coffee, right?<br/><br/>
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
echo("<center>Your coffee was registered</center>");
}
?>

<?php
	include('footer.php');
?>

