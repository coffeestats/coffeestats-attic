<?php
session_start();
if (isset($_SESSION['login_user'])) {
    include("auth/lock.php");
} else {
    include("auth/config.php");
}
include_once("includes/common.php");
include_once("includes/validation.php");

$ownprofile = FALSE;
// Parse user
if (isset($_GET['u'])) {
    if (($profileuser = sanitize_username($_GET['u'])) === FALSE) {
        errorpage('Error', 'Invalid username.', '400 Bad Request');
    }
}
elseif (isset($_SESSION['login_user'])) {
    $ownprofile = TRUE;
    $profileuser = $_SESSION['login_user'];
}
else {
    // not logged in and no profile user specified
    errorpage('Error', 'Invalid request', '400 Bad Request');
}

$sql = sprintf(
    "SELECT uid, ufname, uname, ulocation, utoken FROM cs_users WHERE ulogin = '%s'",
    $dbconn->real_escape_string($profileuser));
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $profileid = $row['uid'];
    $profilename = $row['uname'];
    $profileforename = $row['ufname'];
    $profilelocation = $row['ulocation'];
    $profiletoken = $row['utoken'];
}
else {
    // no result found
    errorpage('Error', 'No profile found', '404 No Profile Found');
}
$result->close();

function total_coffees_for_profile($profileid) {
    global $dbconn;
    $sql = sprintf(
        "SELECT COUNT(cid) AS total FROM cs_coffees WHERE cuid = %d",
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    $total = 0;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $total = $row['total'];
    }
    $result->close();
    return $total;
}

function total_mate_for_profile($profileid) {
    global $dbconn;
    $sql = sprintf(
        "SELECT COUNT(mid) AS total FROM cs_mate WHERE cuid = %d",
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    $total = 0;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $total = $row['total'];
    }
    $result->close();
    return $total;
}

function public_url($profileuser) {
    return sprintf("%s/profile?u=%s", baseurl(), urlencode($profileuser));
}

function on_the_run_url($profileuser, $profiletoken) {
    return sprintf("%s/ontherun?u=%s&t=%s", baseurl(), urlencode($profileuser), urlencode($profiletoken));
}

$wholecoffeestack = total_coffees_for_profile($profileid);
$wholematestack = total_mate_for_profile($profileid);

if ($ownprofile) {
    $info = array(
        'title' => 'Your Profile',
        'data' => array(
            'Name' => sprintf('%s %s', $profileforename, $profilename),
            'Location' => $profilelocation,
            'Your Coffees total' => $wholecoffeestack,
            'Your Mate total' => $wholematestack,
        ),
        'extra' => array(
            sprintf('Your <a href="%s">public profile page</a>', public_url($profileuser)),
            sprintf('Your <a href="%s">on-the-run</a> URL', on_the_run_url($profileuser, $profiletoken)),
        ),
        'afterlist' => sprintf(
          '<a href="https://www.facebook.com/sharer.php?u=%s&t=My%%20coffee%%20statistics"><img src="images/facebook40.png" alt="facebook share icon" /></a>
           <a href="https://twitter.com/intent/tweet?original_referer=%s&text=My%%20coffee%%20statistics&tw_p=tweetbutton&url=%s&via=coffeestats"><img src="images/twitter40.png" alt="twitter share" /></a>
           <a href="https://plus.google.com/share?url=%s"><img src="images/googleplus40.png" alt="google plus share" /></a>',
            urlencode(public_url($profileuser)), urlencode(public_url($profileuser)), urlencode(public_url($profileuser)), urlencode(public_url($profileuser))),
    );
}
else {
    $info = array(
        'title' => sprintf("%s's Profile", $profileuser),
        'data' => array(
            'Name' => sprintf('%s %s', $profileforename, $profilename),
            'Location' => $profilelocation,
            'Coffees total' => $wholecoffeestack,
            'Mate total' => $wholematestack,
        ),
    );
}


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
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $todayrows[intval($row['hour'])][0] = $row['coffees'];
}
$result->close();

$sql = sprintf(
    "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%%H') AS hour
     FROM cs_mate
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP, '%%Y-%%m-%%d') = DATE_FORMAT(mdate, '%%Y-%%m-%%d')
       AND cuid = %d
     GROUP BY hour",
    $profileid);
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $todayrows[intval($row['hour'])][1] = $row['mate'];
}
$result->close();

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
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $monthrows[intval($row['day'])][0] = $row['coffees'];
}
$result->close();

$sql = sprintf(
    "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%%d') AS day
     FROM cs_mate
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y-%%m') = DATE_FORMAT(mdate, '%%Y-%%m')
       AND cuid = %d
     GROUP BY day",
     $profileid);
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $monthrows[intval($row['day'])][1] = $row['mate'];
}
$result->close();

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
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $yearrows[intval($row['month'])][0] = $row['coffees'];
}
$result->close();

$sql = sprintf(
    "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%%m') AS month
     FROM cs_mate
     WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%%Y') = DATE_FORMAT(mdate, '%%Y')
       AND cuid = %d
     GROUP BY month",
    $profileid);
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

$sql = sprintf(
    "SELECT COUNT(cid) AS coffees, DATE_FORMAT(cdate, '%%H') AS hour
     FROM cs_coffees
     WHERE cuid = %d
     GROUP BY hour",
    $profileid);
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $byhourrows[intval($row['hour'])][0] = $row['coffees'];
}
$result->close();

$sql = sprintf(
    "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%%H') AS hour
     FROM cs_mate
     WHERE cuid = %d
     GROUP BY hour",
    $profileid);
if (($result=$dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $byhourrows[intval($row['hour'])][1] = $row['mate'];
}
$result->close();

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
if (($result=$dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row=$result->fetch_array(MYSQLI_ASSOC)) {
    $byweekdayrows[$row['wday']][0] = $row['coffees'];
}
$result->close();

$sql=sprintf(
    "SELECT COUNT(mid) AS mate, DATE_FORMAT(mdate, '%%a') AS wday
     FROM cs_mate
     WHERE cuid = %d
     GROUP BY wday",
    $profileid);
if (($result=$dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
while ($row=$result->fetch_array(MYSQLI_ASSOC)) {
    $byweekdayrows[$row['wday']][1] = $row['mate'];
}
$result->close();

include('includes/charting.php');
include("header.php");
?>
<div class="white-box">
    <h2><?php echo $info['title']; ?></h2>
    <ul>
<?php
foreach ($info['data'] as $key => $value) { ?>
        <li><?php echo $key; ?>: <?php echo $value; ?></li>
<?php
}
if (isset($info['extra'])) {
    foreach ($info['extra'] as $value) { ?>
        <li><?php echo $value; ?></li>
<?php
    }
}
?>
    </ul>
<?php
if (isset($info['afterlist'])) {
    echo $info['afterlist'];
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

<script type="text/javascript" src="./lib/Chart.min.js"></script>
<script type="text/javascript">
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
