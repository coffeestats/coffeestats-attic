<?php
include("auth/lock.php");
include("auth/config.php");
include("header.php");
?>
<div class="white-box">
<h2>Explore!</h2>
<?php
$sql="SELECT uid, ulogin, ufname, uname, ulocation FROM cs_users; ";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      printf("<p><a href=\"profile.php?u=%s\">%s</a> (%s %s) from %s</br>", $row[1], $row[1], $row[2], $row[3], $row[4]);  
      $totalsql="SELECT count(cid) as total FROM cs_coffees WHERE cuid='".$row[0]."';";
      $totalresult=mysql_query($totalsql);
      $totalrow=mysql_fetch_array($totalresult);
      echo("Coffees total: ".$totalrow['total']."</p>");
}

?>
</div>

<?php
	include("footer.php");
?>
