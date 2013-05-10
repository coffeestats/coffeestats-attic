<?php
include("auth/lock.php");
include("lib/antixss.php");


// COFFEE VS MATE CHART
$sql = "SELECT count(cs_coffees.cid) as coffees FROM cs_coffees";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $wholecoffeestack = $row['coffees'];
}
else {
    $wholecoffeestack = 0;
}
$result->close();

$sql="SELECT count(cs_mate.mid) as mate FROM cs_mate";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $wholematestack = $row['mate'];
}
else {
    $wholematestack = 0;
}
$result->close();


// TODAY CHART
$todayrows = array();
for ($counter = 0; $counter <= 23; $counter++) {
    $todayrows[$counter] = array(0, 0);
}

$sql = "SELECT COUNT(cid) AS coffees, DATE_FORMAT(cdate, '%H') AS hour
        FROM cs_coffees
        WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d') = DATE_FORMAT(cdate, '%Y-%m-%d')
        GROUP BY hour";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $todayrows[intval($row['hour'])][0] = $row['coffees'];
}
$result->close();

$sql = "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%H') AS hour
        FROM cs_mate
        WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d') = DATE_FORMAT(mdate, '%Y-%m-%d')
        GROUP BY hour";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $todayrows[intval($row['hour'])][1] = $row['mate'];
}
$result->close();

// MONTH CHART
$monthrows = array();
$now = getdate();
$maxdays = cal_days_in_month(CAL_GREGORIAN, $now['mon'], $now['year']);
for ($counter = 1; $counter <= $maxdays; $counter++) {
    $monthrows[$counter] = array(0, 0);
}

$sql = "SELECT COUNT(cid) AS coffees, DATE_FORMAT(cdate, '%d') AS day
        FROM cs_coffees
        WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m') = DATE_FORMAT(cdate, '%Y-%m')
        GROUP BY day";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $monthrows[intval($row['day'])][0] = $row['coffees'];
}
$result->close();

$sql = "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%d') AS day
        FROM cs_mate
        WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m') = DATE_FORMAT(mdate, '%Y-%m')
        GROUP BY day";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $monthrows[intval($row['day'])][1] = $row['mate'];
}
$result->close();

// YEAR CHART
$yearrows = array();
for ($counter = 1; $counter <= 12; $counter++) {
    $yearrows[$counter] = array(0, 0);
}

$sql = "SELECT COUNT(cid) AS coffees, DATE_FORMAT(cdate,'%m') AS month
        FROM cs_coffees
        WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y') = DATE_FORMAT(cdate, '%Y')
        GROUP BY month";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $yearrows[intval($row['month'])][0] = $row['coffees'];
}

$sql = "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%m') AS month
        FROM cs_mate
        WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y') = DATE_FORMAT(mdate, '%Y')
        GROUP BY month";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $yearrows[intval($row['month'])][1] = $row['mate'];
}
$result->close();

// BY HOUR
$byhourrows = array();
for ($counter = 0; $counter <= 23; $counter++) {
    $byhourrows[$counter] = array(0, 0);
}

$sql = "SELECT COUNT(cid) AS coffees, DATE_FORMAT(cdate, '%H') AS hour
        FROM cs_coffees
        GROUP BY hour";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $byhourrows[intval($row['hour'])][0] = $row['coffees'];
}
$result->close();

$sql = "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%H') AS hour
        FROM cs_mate
        GROUP BY hour";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row=$result->fetch_array(MYSQLI_ASSOC)) {
    $byhourrows[intval($row['hour'])][1] = $row['mate'];
}
$result->close();

// BY WEEKDAY
$byweekdayrows = array();
$weekdays = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
for ($counter = 0; $counter < count($weekdays); $counter++) {
    $byweekdayrows[$weekdays[$counter]] = array(0, 0);
}

$sql = "SELECT COUNT(cid) AS coffees, DATE_FORMAT(cdate, '%a') AS wday
        FROM cs_coffees
        GROUP BY wday";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $byweekdayrows[$row['wday']][0] = $row['coffees'];
}
$result->close();

$sql = "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%a') AS wday
        FROM cs_mate
        GROUP BY wday";
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $byweekdayrows[$row['wday']][1] = $row['mate'];
}
$result->close();

include('includes/charting.php');
include("header.php");
?>
<div class="white-box">
  <h2>Overall Statistics</h2>

  <p>We love stats. On overall statistics we started making awesome graphs examining the daily coffee
     consumption of anyone using coffeestats.org. There are different approaches to visualize this. At least a few of them are listed below.</p>

  <p>Hint: Yellow will always be Mate.</p>
</div>
<div class="white-box">
  <h2>Caffeine today</h2>
  <canvas id="coffeetoday" width="590" height="240" ></canvas>
</div>
<div class="white-box">
  <h2>Caffeine this month</h2>
  <canvas id="coffeemonth" width="590" height="240" ></canvas>
</div>
<div class="white-box">
  <h2>Coffees vs. Mate</h2>
  <canvas id="coffeevsmate" width="590" height="240" ></canvas>
</div>
<div class="white-box">
  <h2>Caffeine this year</h2>
  <canvas id="coffeeyear" width="590" height="240" ></canvas>
</div>
<div class="white-box">
  <h2>Caffeine by hour (overall)</h2>
  <canvas id="coffeebyhour" width="590" height="240" ></canvas>
</div>
<div class="white-box">
  <h2>Caffeine by weekday (overall)</h2>
  <canvas id="coffeebyweekday" width="590" height="240" ></canvas>
</div>

<script src="./lib/Chart.min.js"></script>

<script>
    var todaycolor = "#E64545"
    var monthcolor = "#FF9900"
    var yearcolor = "#3399FF"
    var hourcolor = "#FF6666"
    var weekdaycolor = "#A3CC52"
    var matecolor = "#FFCC00"
    var matelightcolor = "#FFE066"
    var barChartData;
    var lineChartData;

    var doughnutData = [
        {
            value: <?php echo($wholecoffeestack); ?>,
            color: todaycolor
        },
        {
            value: <?php echo($wholematestack); ?>,
            color : matecolor
        }
    ];
    new Chart(document.getElementById("coffeevsmate").getContext("2d")).Doughnut(doughnutData);

    barChartData = {
        labels: [<?php extractlabels($todayrows); ?>],
        datasets : [
            {
                fillColor: todaycolor,
                strokeColor: todaycolor,
                data: [<?php extractdata($todayrows, 0); ?>],
            },
            {
                fillColor: matecolor,
                strokeColor: matecolor,
                data: [<?php extractdata($todayrows, 1); ?>],
            },
        ]
    }
    new Chart(document.getElementById("coffeetoday").getContext("2d")).Bar(barChartData);

    lineChartData = {
        labels: [<?php extractlabels($monthrows); ?>],
        datasets : [
            {
                fillColor: monthcolor,
                strokeColor: "#FFB84D",
                pointColor: "#FFB84D",
                pointStrokeColor: "#fff",
                data: [<?php extractdata($monthrows, 0); ?>],
            },
            {
                fillColor: matecolor,
                strokeColor: matelightcolor,
                pointColor: matelightcolor,
                pointStrokeColor: "#fff",
                data: [<?php extractdata($monthrows, 1); ?>],
            },
        ]
    }
    new Chart(document.getElementById("coffeemonth").getContext("2d")).Line(lineChartData);

    barChartData = {
        labels: [<?php extractlabels($yearrows); ?>],
        datasets : [
            {
                fillColor: yearcolor,
                strokeColor: yearcolor,
                data: [<?php extractdata($yearrows, 0); ?>],
            },
            {
                fillColor: matecolor,
                strokeColor: matecolor,
                data: [<?php extractdata($yearrows, 1); ?>],
            },
        ]
    }
    new Chart(document.getElementById("coffeeyear").getContext("2d")).Bar(barChartData);

    lineChartData = {
        labels: [<?php extractlabels($byhourrows); ?>],
        datasets : [
            {
                fillColor: hourcolor,
                strokeColor: "#FF9999",
                pointColor: "#FF9999",
                pointStrokeColor: "#fff",
                data: [<?php extractdata($byhourrows, 0); ?>],
            },
            {
                fillColor: matecolor,
                strokeColor: matelightcolor,
                pointColor: matelightcolor,
                pointStrokeColor: "#fff",
                data: [<?php extractdata($byhourrows, 1); ?>],
            },
        ]
    }
    new Chart(document.getElementById("coffeebyhour").getContext("2d")).Line(lineChartData);

    lineChartData = {
        labels: [<?php extractlabels($byweekdayrows); ?>],
        datasets: [
            {
                fillColor: weekdaycolor,
                strokeColor: "#99FF99",
                pointColor: "#99FF99",
                pointStrokeColor: "#fff",
                data: [<?php extractdata($byweekdayrows, 0); ?>],
            },
            {
                fillColor: matecolor,
                strokeColor: matelightcolor,
                pointColor: matelightcolor,
                pointStrokeColor: "#fff",
                data: [<?php extractdata($byweekdayrows, 1); ?>],
            },
        ]
    }
    new Chart(document.getElementById("coffeebyweekday").getContext("2d")).Line(lineChartData);
</script>

<?php
include("footer.php");
?>
