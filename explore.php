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
$sql = "SELECT cs_users.ulogin AS ulogin, cs_coffees.cdate AS cdate FROM cs_coffees,cs_users WHERE cs_coffees.cuid = cs_users.uid ORDER BY cid DESC LIMIT 10";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    printf("<li><a href=\"profile.php?u=%s\">%s</a> at %s<br></li>", urlencode($row['ulogin']), $row['ulogin'], $row['cdate']);
}
$result->close();
?>
</ul>
</div>

<div class="white-box">
<h2>Get in touch with eachother!</h2>
<div id="random_users">
<?php
$sql = "SELECT ulogin, ufname, uname, ulocation,
        (SELECT COUNT(cid) FROM cs_coffees WHERE cuid=uid) AS coffees,
        (SELECT COUNT(mid) FROM cs_mate WHERE cuid=uid) AS mate
        FROM cs_users ORDER BY RAND() LIMIT 4";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
$users = array();
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    array_push($users, $row);
}
$result->close();

foreach ($users as $user) { ?>
    <div class="usercard">
        <a href="profile.php?u=<?php echo urlencode($user['ulogin']); ?>"><?php echo $user['ulogin']; ?></a><br />
        Name: <?php printf("%s %s", $user['ufname'], $user['uname']); ?><br />
        Location: <?php echo $user['ulocation']; ?><br />
        Coffees total: <?php echo $user['coffees']; ?><br />
        Mate total: <?php echo $user['mate']; ?>
    </div>
<?php
}
?>
</div>
<div class="clearfix">&nbsp;</div>
</div>

<div class="white-box">
<h2>Ranking</h2>
<table  width=500 height=200>
  <tr>
  <td width=50%>
    Coffees Summary
    <ul>
<?php
$sql = "SELECT COUNT(cid) AS total, cs_users.ulogin AS ulogin FROM cs_coffees,cs_users WHERE cs_coffees.cuid = cs_users.uid GROUP BY cs_users.ulogin ORDER BY COUNT(cid) DESC LIMIT 10";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      printf("<li><a href=\"profile.php?u=%s\">%s</a> - %s Coffees</li>", urlencode($row['ulogin']), $row['ulogin'], $row['total']);
}
?>
    </ul>
  </td>
<td width=50%>
Average Coffees a day
<ul>
<?php
$sql = "SELECT cs_users.ulogin AS ulogin, (
    (SELECT COUNT(cid) FROM cs_coffees WHERE cuid = uid)/DATEDIFF(NOW(), (
        SELECT cs_coffees.cdate from cs_coffees WHERE cuid = uid ORDER BY cdate limit 1))) AS Coffeesaday
    FROM cs_users ORDER BY Coffeesaday DESC LIMIT 10";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      printf("<li><a href=\"profile.php?u=%s\">%s</a> - %s</li>", urlencode($row['ulogin']), $row['ulogin'], $row['Coffeesaday']);
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
$sql = "SELECT ulogin FROM cs_users ORDER BY ujoined DESC LIMIT 5";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      printf("<li><a href=\"profile.php?u=%s\">%s</a></li>", urlencode($row['ulogin']), $row['ulogin']);
}
?>
</ul>
</div>


<?php
include("footer.php");
?>
