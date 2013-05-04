<?php
// TODO: rework (see https://bugs.n0q.org/view.php?id=21)

include("auth/lock.php");
include("header.php");
?>
<div class="white-box">
<h2>Explore!</h2>
You're not the only human at this site! Great, isn't it? Lets see the stats of some other guys.
</div>
<div class="white-box">
<h2>Caffeine Activity</h2>
<ul>
<?php
$sql="SELECT cs_users.ulogin,cs_coffees.cdate FROM cs_coffees,cs_users WHERE cs_coffees.cuid = cs_users.uid ORDER BY cid DESC LIMIT 10";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      printf("<li><a href=\"profile.php?u=%s\">%s</a> at %s<br></li>", $row[0], $row[0], $row[1]);
}
?>
</ul>
</div>

<div class="white-box">
<h2>Get in touch with eachother!</h2>

<table width=500 height=200>
<tr >
 <td width=50%>
<?php
$sql="SELECT uid, ulogin, ufname, uname, ulocation FROM cs_users ORDER BY RAND() LIMIT 1";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      printf("<a href=\"profile.php?u=%s\">%s</a><br/>Name: %s %s <br/> Location:  %s</br>", $row[1], $row[1], $row[2], $row[3], $row[4]);
      $totalsql=sprintf(
          "SELECT count(cid) as total FROM cs_coffees WHERE cuid=%d",
          $row[0]);
      $totalresult=mysql_query($totalsql);
      $totalrow=mysql_fetch_array($totalresult);
      echo("Coffees total: ".$totalrow['total']." ");
}
?>
<br/><br/></td>
<td width=50%>

<?php
$sql="SELECT uid, ulogin, ufname, uname, ulocation FROM cs_users ORDER BY RAND() LIMIT 1";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      printf("<a href=\"profile.php?u=%s\">%s</a><br/>Name: %s %s <br/> Location:  %s</br>", $row[1], $row[1], $row[2], $row[3], $row[4]);
      $totalsql=sprintf(
          "SELECT count(cid) as total FROM cs_coffees WHERE cuid=%d",
          $row[0]);
      $totalresult=mysql_query($totalsql);
      $totalrow=mysql_fetch_array($totalresult);
      echo("Coffees total: ".$totalrow['total']." ");
}
?>
<br/><br/></td>
</tr>
<tr>
<td width=50%>
<?php
$sql="SELECT uid, ulogin, ufname, uname, ulocation FROM cs_users ORDER BY RAND() LIMIT 1";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      printf("<a href=\"profile.php?u=%s\">%s</a><br/>Name: %s %s <br/> Location:  %s</br>", $row[1], $row[1], $row[2], $row[3], $row[4]);
      $totalsql=sprintf(
          "SELECT count(cid) as total FROM cs_coffees WHERE cuid=%d",
          $row[0]);
      $totalresult=mysql_query($totalsql);
      $totalrow=mysql_fetch_array($totalresult);
      echo("Coffees total: ".$totalrow['total']." ");
}
?>
<br/><br/></td>
<td width=50%>
<?php
$sql="SELECT uid, ulogin, ufname, uname, ulocation FROM cs_users ORDER BY RAND() LIMIT 1";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      printf("<a href=\"profile.php?u=%s\">%s</a><br/>Name: %s %s <br/> Location:  %s</br>", $row[1], $row[1], $row[2], $row[3], $row[4]);
      $totalsql=sprintf(
          "SELECT count(cid) as total FROM cs_coffees WHERE cuid=%d",
          $row[0]);
      $totalresult=mysql_query($totalsql);
      $totalrow=mysql_fetch_array($totalresult);
      echo("Coffees total: ".$totalrow['total']." ");
}
?>
<br/><br/></td>
</tr>
</table>
</div>

<div class="white-box">
<h2>Ranking</h2>
<table  width=500 height=200>
  <tr>
  <td width=50%>
    Coffees Summary
    <ul>
<?php
$sql="SELECT COUNT(cid), cs_users.ulogin FROM cs_coffees,cs_users WHERE cs_coffees.cuid = cs_users.uid GROUP BY cs_users.ulogin ORDER BY COUNT(cid) DESC LIMIT 10";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      printf("<li><a href=\"profile.php?u=%s\">%s</a> - %s Coffees</li>", $row[1], $row[1], $row[0]);
}
?>
    </ul>
  </td>
<td width=50%>
Average Coffees a day
<ul>
<?php
$sql="SELECT cs_users.ulogin, (
    (SELECT COUNT(cid) FROM cs_coffees WHERE cuid = uid)/DATEDIFF(NOW(), (
        SELECT cs_coffees.cdate from cs_coffees WHERE cuid = uid ORDER BY cdate limit 1))) AS Coffeesaday
    FROM cs_users ORDER BY Coffeesaday DESC LIMIT 10";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      printf("<li><a href=\"profile.php?u=%s\">%s</a> - %s</li>", $row[0], $row[0], $row[1]);
}
?>
</ul>
</td>
</table>
</ul>
</div>


<div class="white-box">
<h2>Recently registered</h2>
<ul>
<?php
$sql="SELECT ulogin FROM cs_users ORDER BY uid DESC LIMIT 5";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
      printf("<li><a href=\"profile.php?u=%s\">%s</a></li>", $row[0], $row[0]);
}
?>
</ul>
</div>


<?php
	include("footer.php");
?>
