<?php
include("auth/lock.php");
// include("auth/config.php"); # already included in auth/lock.php
include("header.php");
include("lib/antixss.php");

// Parse user
if (isset($_GET['u'])) {
    $profileuser=AntiXSS::setFilter($_GET['u'], "whitelist", "string");
    $profileuser=mysql_real_escape_string($profileuser);
    $sql=sprintf(
        "SELECT uid, ufname, uname, ulocation, utoken FROM cs_users WHERE ulogin='%s'",
        $profileuser);
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    $count=mysql_num_rows($result);
    $profileid=$row['uid'];
    $profilename=$row['uname'];
    $profileforename=$row['ufname'];
    $profilelocation=$row['ulocation'];
    $profiletoken=$row['utoken'];
} else {
    $count=0;
}

?>
<div class="white-box">
<?php
if ($count==1) {
  if ($profileid==$_SESSION['login_id']) {
    echo ("<h2>Your Profile </h2>");
    echo ("<ul>");
    echo("<li>Name: $profileforename $profilename </li>");
    echo("<li>Location: $profilelocation </li>");
    $sql=sprintf(
        "SELECT count(cid) as total FROM cs_coffees WHERE cuid=%d",
        $profileid);
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    echo("<li>Your Coffees total: ".$row['total']."</li>");
    $sql=sprintf(
        "SELECT count(mid) as total FROM cs_mate WHERE cuid=%d",
        $profileid);
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    echo("<li>Your Mate total: ".$row['total']."</li>");
    echo("<li>Your <a href=\"http://coffeestats.org/public?u=".$_SESSION['login_user']."\">public profile page</a></li>");
    echo("<li>Your <a href=\"http://coffeestats.org/ontherun?u=".$_SESSION['login_user']."&t=".$profiletoken."\">on-the-run</a> URL</li>");
    echo ("</ul>");
    echo("Share your profile on Facebook! <br>
      <br/><a href=\"http://www.facebook.com/sharer.php?u=http://coffeestats.org/public?u=".$_SESSION['login_user']."&t=My%20coffee%20statistic\"><img src=\"images/facebook-share-icon.gif\"></a></li>");
  } else {
    echo ("<h2>".$profileuser."'s Profile</h2>");
    echo ("<ul>");
    echo("<li>Name: $profileforename $profilename </li>");
    echo("<li>Location: $profilelocation </li>");
    $sql=sprintf(
        "SELECT count(cid) as total FROM cs_coffees WHERE cuid=%d",
        $profileid);
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    echo("<li>Coffees total: ".$row['total']."</li>");
    $sql=sprintf(
        "SELECT count(mid) as total FROM cs_mate WHERE cuid=%d",
        $profileid);
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    echo("<li>Mate total: ".$row['total']."</li>");
    echo ("</ul>");
  }
} else {
  $profileid=$_SESSION['login_id'];
  echo ("<h2>Your Profile</h2>");
  echo("Error finding User. Showing your Graphs instead.");
}

// COFFEE VS MATE CHART
$sql=sprintf(
    "SELECT count(cs_coffees.cid) as coffees FROM cs_coffees WHERE cuid=%d",
    $profileid);
$result=mysql_query($sql);
$row = mysql_fetch_array($result);
$wholecoffeestack = $row['coffees'];

$sql=sprintf(
    "SELECT count(cs_mate.mid) as mate FROM cs_mate WHERE cuid=%d",
    $profileid);
$result=mysql_query($sql);
$row = mysql_fetch_array($result);
$wholematestack = $row['mate'];

// TODAY
$todayrows = array();
for ($counter = 0; $counter <= 23; $counter++) {
    $todayrows[$counter] = array(0, 0);
}

$sql = sprintf(
    "SELECT COUNT(cid) AS coffees, DATE_FORMAT(cdate, '%%H') AS hour
     FROM cs_coffees
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP, '%%Y-%%m-%%d') = DATE_FORMAT(cdate, '%%Y-%%m-%%d')
       AND cuid = %d
     GROUP BY hour",
     $profileid);
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    $todayrows[intval($row['hour'])][0] = $row['coffees'];
}

$sql = sprintf(
    "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%%H') AS hour
     FROM cs_mate
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP, '%%Y-%%m-%%d') = DATE_FORMAT(mdate, '%%Y-%%m-%%d')
       AND cuid = %d
     GROUP BY hour",
    $profileid);
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    $todayrows[intval($row['hour'])][1] = $row['mate'];
}

// MONTH
$monthrows = array();
$now = getdate();
$maxdays = cal_days_in_month(CAL_GREGORIAN, $now['mon'], $now['year']);
for ($counter = 1; $counter <= $maxdays; $counter++) {
    $monthrows[$counter] = array(0, 0);
}

$sql = sprintf(
    "SELECT COUNT(cid) AS coffees, DATE_FORMAT(cdate, '%%d') AS day
     FROM cs_coffees
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y-%%m') = DATE_FORMAT(cdate, '%%Y-%%m')
       AND cuid = %d
     GROUP BY day",
     $profileid);
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    $monthrows[intval($row['day'])][0] = $row['coffees'];
}

$sql = sprintf(
    "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%%d') AS day
     FROM cs_mate
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y-%%m') = DATE_FORMAT(mdate, '%%Y-%%m')
       AND cuid = %d
     GROUP BY day",
     $profileid);
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    $monthrows[intval($row['day'])][1] = $row['mate'];
}

// YEAR
$yearrows = array();
for ($counter = 1; $counter <= 12; $counter++) {
    $yearrows[$counter] = array(0, 0);
}

$sql = sprintf(
    "SELECT COUNT(cid) AS coffees, DATE_FORMAT(cdate,'%%m') AS month
     FROM cs_coffees
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%%Y') = DATE_FORMAT(cdate, '%%Y')
       AND cuid = %d
     GROUP BY month",
    $profileid);
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    $yearrows[intval($row['month'])][0] = $row['coffees'];
}

$sql = sprintf(
    "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%%m') AS month
     FROM cs_mate
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%%Y') = DATE_FORMAT(mdate, '%%Y')
       AND cuid = %d
     GROUP BY month",
    $profileid);
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    $yearrows[intval($row['month'])][1] = $row['mate'];
}

// BY HOUR
$byhourrows = array();
for ($counter = 0; $counter <= 23; $counter++) {
    $byhourrows[$counter] = array(0, 0);
}

$sql = sprintf(
    "SELECT COUNT(cid) AS coffees, DATE_FORMAT(cdate, '%%H') AS hour
     FROM cs_coffees
     WHERE cuid = %d
     GROUP BY hour",
    $profileid);
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    $byhourrows[intval($row['hour'])][0] = $row['coffees'];
}

$sql = sprintf(
    "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%%H') AS hour
     FROM cs_mate
     WHERE cuid = %d
     GROUP BY hour",
    $profileid);
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
    $byhourrows[intval($row['hour'])][1] = $row['mate'];
}

// BY WEEKDAY
$byweekdayrows = array();
$weekdays = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
for ($counter = 0; $counter < count($weekdays); $counter++) {
    $byweekdayrows[$weekdays[$counter]] = array(0, 0);
}

$sql=sprintf(
    "SELECT COUNT(cid) AS coffees, DATE_FORMAT(cdate, '%%a') AS wday
     FROM cs_coffees
     WHERE cuid = %d
     GROUP BY wday",
    $profileid);
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result)) {
    $byweekdayrows[$row['wday']][0] = $row['coffees'];
}

$sql=sprintf(
    "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%%a') AS wday
     FROM cs_mate
     WHERE cuid = %d
     GROUP BY wday",
    $profileid);
$result=mysql_query($sql);
while ($row=mysql_fetch_array($result)) {
    $byweekdayrows[$row['wday']][1] = $row['mate'];
}

function extractlabels(&$assocarray) {
    $labels = array();
    foreach (array_keys($assocarray) as $key) {
        array_push($labels, sprintf("'%s'", $key));
    }
    print(implode(',', $labels));
}

function extractdata(&$assocarray, $field) {
    $data = array();
    foreach ($assocarray as $key => $value) {
        array_push($data, $assocarray[$key][$field]);
    }
    print(implode(',', $data));
}
?>
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
    var todaycolor = "#E64545";
    var monthcolor = "#FF9900";
    var yearcolor = "#3399FF";
    var hourcolor = "#FF6666";
    var weekdaycolor = "#A3CC52";
    var matecolor = "#FFCC00";
    var matelightcolor = "#FFE066";
    var barChartData;
    var lineChartData;

    var doughnutData = [
        {
            value: <?php echo($wholecoffeestack); ?>,
            color: todaycolor
        },
        {
            value: <?php echo($wholematestack); ?>,
            color: matecolor
        }
    ];
    new Chart(document.getElementById("coffeevsmate").getContext("2d")).Doughnut(doughnutData);

    barChartData = {
        labels: [<?php extractlabels($todayrows); ?>],
        datasets: [
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
        datasets: [
            {
                fillColor: yearcolor,
                strokeColor: yearcolor,
                data: [<?php extractdata($yearrows, 0); ?>],
            },
            {
                fillColor: matecolor,
                strokeColor : matecolor,
                data: [<?php extractdata($yearrows, 1); ?>],
            },
        ]
    }
    new Chart(document.getElementById("coffeeyear").getContext("2d")).Bar(barChartData);

    lineChartData = {
        labels: [<?php extractlabels($byhourrows); ?>],
        datasets: [
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
