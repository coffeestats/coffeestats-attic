<?php
// TODO: do some real comparison (see https://bugs.n0q.org/view.php?id=23)

include("auth/lock.php");
include("header.php");
include("lib/antixss.php");

// Parse user
if (isset($_GET['u'])) {
    $profileuser = AntiXSS::setFilter($_GET['u'], "whitelist", "string");
    $sql = sprintf(
        "SELECT uid FROM cs_users WHERE ulogin='%s'",
        $dbconn->real_escape_string($profileuser));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $profileid = $row['uid'];
    }
    $result->close();
}
?>
<div class="white-box">
<?php
// TODO: handle similarly to profile.php
if (isset($profileid)) {
    if ($profileid == $_SESSION['login_id']) {
        echo("<h2>Your Profile</h2>");
    }
    else {
        echo("<h2>".$profileuser."'s Profile</h2>");
    }
}
else {
    $profileid=$_SESSION['login_id'];
    echo ("<h2>Your Profile</h2>");
    echo("Error finding User. Showing your Graphs instead.");
}

// total
$sql = sprintf(
    "SELECT count(cid) AS total
     FROM cs_coffees
     WHERE cuid=%d",
     $profileid);
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $totalcoffees = $row['total'];
}
$result->close();

/**
 * print an associative array with a specified element count for a chart row.
 */
function chartrows($data, $begin, $maxcount) {
    for ($counter = $begin; $counter <= $maxcount; $counter++) {
        printf("['%s',%d],", $counter, $data[$counter]);
    }
}
?>
  Coffees total: <?php echo $totalcoffees; ?>
</div>
<div class="white-box">
  <div id="coffee_today"></div>
</div>
<div class="white-box">
  <div id="coffee_month"></div>
</div>
<div class="white-box">
  <div id="coffee_year"></div>
</div>

<?php
// queries for charts

// today
$maxhours = 23;
$hourrows = array();
for ($counter = 0; $counter <= $maxhours; $counter++) {
    $hourrows[$counter] = 0;
}
$sql = sprintf(
    "SELECT DATE_FORMAT(cdate, '%%H') AS hour, COUNT(cid) AS coffees
     FROM cs_coffees
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP, '%%Y-%%m-%%d') = DATE_FORMAT(cdate, '%%Y-%%m-%%d')
       AND cuid = %d
     GROUP BY hour",
    $profileid);
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $hourrows[intval($row['hour'])] = $row['coffees'];
}
$result->close();

// month
$now = getdate();
$maxdays = cal_days_in_month(CAL_GREGORIAN, $now['mon'], $now['year']);
$dayrows = array();
for ($counter = 1; $counter <= $maxdays; $counter ++) {
    $dayrows[$counter] = 0;
}
$sql = sprintf(
    "SELECT DATE_FORMAT(cdate, '%%d') AS day, COUNT(cid) AS coffees
     FROM cs_coffees
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP, '%%Y-%%m') = DATE_FORMAT(cdate, '%%Y-%%m')
       AND cuid = %d
     GROUP BY day",
     $profileid);
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $dayrows[intval($row['day'])] = $row['coffees'];
}
$result->close();

// year
$maxmonths = 12;
$monthrows = array();
for ($counter = 1; $counter <= $maxmonths; $counter++) {
    $monthrows[$counter] = 0;
}
$sql = sprintf(
    "SELECT DATE_FORMAT(cdate, '%%m') AS month, COUNT(cid) AS coffees
     FROM cs_coffees
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP, '%%Y') = DATE_FORMAT(cdate, '%%Y')
       AND cuid = %d
     GROUP BY month",
    $profileid);
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $monthrows[intval($row['month'])] = $row['coffees'];
}
$result->close();

?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Hour');
        data.addColumn('number', 'Coffees');
        data.addRows([<?php chartrows($hourrows, 0, $maxhours); ?>]);

        var options = {
            width: 550, height: 240,
            title: 'Your coffees today',
            hAxis: {title: 'Hour'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('coffee_today'));
        chart.draw(data, options);

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Month');
        data.addColumn('number', 'Coffees');
        data.addRows([<?php chartrows($dayrows, 1, $maxdays); ?>]);

        var options = {
          width: 550, height: 240,
          title: 'Your coffees this month',
          hAxis: {title: 'Day'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('coffee_month'));
        chart.draw(data, options);

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Day');
        data.addColumn('number', 'Coffees');
        data.addRows([<?php chartrows($monthrows, 1, $maxmonths); ?>]);

        var options = {
          width: 550, height: 240,
          title: 'Your coffees this year',
          hAxis: {title: 'Year'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('coffee_year'));
        chart.draw(data, options);
    }
</script>

<?php
include("footer.php");
?>
