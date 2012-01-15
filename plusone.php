<?php
include('auth/lock.php');
include("header.php"); 
?>
<center>
<form action="" method="post">
<input style="height: 75px; width: 200px"  type="submit" value="Mhh. Coffee. It tastes awesome"/><br />
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

