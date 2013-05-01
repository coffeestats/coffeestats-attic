<?php
include("auth/lock.php");
// include("auth/config.php"); # already included in auth/lock.php
include("header.php");
include("lib/antixss.php");


// COFFEE VS MATE CHART
$sql="SELECT count(cs_coffees.cid) as coffees FROM cs_coffees";
$result=mysql_query($sql);
$row = mysql_fetch_array($result);
$wholecoffeestack = $row['coffees'];

$sql="SELECT count(cs_mate.mid) as mate FROM cs_mate";
$result=mysql_query($sql);
$row = mysql_fetch_array($result);
$wholematestack = $row['mate'];


// TODAY CHART
$ctodaystack = array();
$mtodaystack = array();
$htodaystack = array();
for ( $counter = 0; $counter <= 23; $counter += 1) {
    $sql=sprintf(
        "SELECT count(cid) as coffees, '%d' as hour
         FROM cs_coffees
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y-%%m-%%d') = DATE_FORMAT(cdate, '%%Y-%%m-%%d')
           AND DATE_FORMAT(cdate, '%%H') = '%02d'",
           $counter, $counter);
  $result=mysql_query($sql);
  $row = mysql_fetch_array($result);
  array_push($ctodaystack, $row['coffees']);
  array_push($htodaystack, $row['hour']);

  $sql=sprintf(
      "SELECT count(mid) as mate, '%d' as hour
       FROM cs_mate
       WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y-%%m-%%d') = DATE_FORMAT(mdate, '%%Y-%%m-%%d')
         AND DATE_FORMAT(mdate, '%%H') = '%02d'",
      $counter, $counter);
  $result=mysql_query($sql);
  $row = mysql_fetch_array($result);
  array_push($mtodaystack, $row['mate']);
}

// MONTH CHART
$cmonthstack = array();
$mmonthstack = array();
$dmonthstack = array();
for ( $counter = 1; $counter <= 30; $counter += 1) {
    $sql=sprintf(
        "SELECT '%d' AS day, count(cid) AS coffees
         FROM cs_coffees
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y-%%m') = DATE_FORMAT(cdate, '%%Y-%%m')
           AND DATE_FORMAT(cdate,'%%d') = '%02d'",
        $counter, $counter);
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    array_push($cmonthstack, $row['coffees']);
    array_push($dmonthstack, $row['day']);

    $sql=sprintf(
        "SELECT '".$counter."' AS day, count(mid) AS mate
         FROM cs_mate
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y-%%m') = DATE_FORMAT(mdate, '%%Y-%%m')
         AND DATE_FORMAT(mdate,'%%d') = '%02d'",
        $counter, $counter);
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    array_push($mmonthstack, $row['mate']);
}

// YEAR CHART
$cyearstack = array();
$myearstack = array();
$mateyearstack = array();
for ( $counter = 1; $counter <= 12; $counter += 1) {
    $sql=sprintf(
        "SELECT '%d' AS month, count(cid) AS coffees
         FROM cs_coffees
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y') = DATE_FORMAT(cdate, '%%Y')
           AND DATE_FORMAT(cdate,'%%m') = '%02d'",
        $counter, $counter);
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    array_push($cyearstack, $row['coffees']);
    array_push($myearstack, $row['month']);

    $sql=sprintf(
        "SELECT '%d' AS month, count(mid) AS mate
         FROM cs_mate
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y') = DATE_FORMAT(mdate, '%%Y')
           AND DATE_FORMAT(mdate, '%%m') = '%02d'",
        $counter, $counter);
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    array_push($mateyearstack, $row['mate']);
}

// BY HOUR
$cbyhourstack = array();
$hbyhourstack = array();
$mbyhourstack = array();
for ( $counter = 0; $counter <= 23; $counter += 1) {
    $sql=sprintf(
        "SELECT '%d' AS hour, count(cid) AS coffees
         FROM cs_coffees
         WHERE DATE_FORMAT(cdate,'%%H') = '%02d'",
        $counter, $counter);
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    array_push($cbyhourstack, $row['coffees']);
    array_push($hbyhourstack, $row['hour']);

    $sql=sprintf(
        "SELECT '%d' as hour, count(mid) as mate
         FROM cs_mate
         WHERE DATE_FORMAT(mdate,'%%H') = '%02d'",
        $counter, $counter);
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    array_push($mbyhourstack, $row['mate']);
}

// BY WEEKDAY
$cbydaystack = array();
$hbydaystack = array();
$mbydaystack = array();
$sql="SELECT DATE_FORMAT(cdate, '%a') as day, count(cid) as coffees
      FROM cs_coffees
      GROUP BY day
      ORDER BY DATE_FORMAT(cdate, '%w')";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
array_push($cbydaystack, $row[1]);
array_push($hbydaystack, $row[0]);
}
$sql="SELECT DATE_FORMAT(mdate, '%a') as day, count(mid) as mate
      FROM cs_mate
      GROUP BY day
      ORDER BY DATE_FORMAT(mdate, '%w')";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    array_push($mbydaystack, $row[1]);
}
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

<?php
function chartarray($variable, $format="%s,") {
    echo("[");
    foreach ($variable as &$value) {
        printf($format, $value);
    }
    unset($value);
    echo("],\n");
}
?>
<script src="./lib/Chart.min.js"></script>

<script>
    var todaycolor = "#E64545"
    var monthcolor = "#FF9900"
    var yearcolor = "#3399FF"
    var hourcolor = "#FF6666"
    var weekdaycolor = "#A3CC52"
    var matecolor = "#FFCC00"
    var matelightcolor = "#FFE066"

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

    var myDoughnut = new Chart(document.getElementById("coffeevsmate").getContext("2d")).Doughnut(doughnutData);

    var barChartData = {
        labels: <?php chartarray($htodaystack); ?>
        datasets : [
            {
                fillColor : todaycolor,
                strokeColor : todaycolor,
                data: <?php chartarray($ctodaystack); ?>
            },
            {
                fillColor : matecolor,
                strokeColor : matecolor,
                data: <?php chartarray($mtodaystack); ?>
            },]
    }

    var myLine = new Chart(document.getElementById("coffeetoday").getContext("2d")).Bar(barChartData);

    var lineChartData = {
        labels: <?php chartarray($dmonthstack); ?>
        datasets : [
            {
                fillColor : monthcolor,
                strokeColor : "#FFB84D",
                pointColor : "#FFB84D",
                pointStrokeColor : "#fff",
                data: <?php chartarray($cmonthstack); ?>
            },
            {
                fillColor : matecolor,
                strokeColor : matelightcolor,
                pointColor : matelightcolor,
                pointStrokeColor : "#fff",
                data: <?php chartarray($mmonthstack); ?>
            },
        ]
    }

    var myLine = new Chart(document.getElementById("coffeemonth").getContext("2d")).Line(lineChartData);

    var barChartData = {
        labels: <?php chartarray($myearstack); ?>
        datasets : [
            {
                fillColor : yearcolor,
                strokeColor : yearcolor,
                data: <?php chartarray($cyearstack); ?>
            },
            {
                fillColor : matecolor,
                strokeColor : matecolor,
                data: <?php chartarray($mateyearstack); ?>
            },
        ]
    }

    var myLine = new Chart(document.getElementById("coffeeyear").getContext("2d")).Bar(barChartData);

    var lineChartData = {
        labels: <?php chartarray($hbyhourstack); ?>
        datasets : [
            {
                fillColor : hourcolor,
                strokeColor : "#FF9999",
                pointColor : "#FF9999",
                pointStrokeColor : "#fff",
                data: <?php chartarray($cbyhourstack); ?>
            },
            {
                fillColor : matecolor,
                strokeColor : matelightcolor,
                pointColor : matelightcolor,
                pointStrokeColor : "#fff",
                data: <?php chartarray($mbyhourstack); ?>
            },
        ]
    }

    var myLine = new Chart(document.getElementById("coffeebyhour").getContext("2d")).Line(lineChartData);

    var lineChartData = {
        labels: <?php chartarray($hbydaystack, "'%s',"); ?>
        datasets: [
            {
                fillColor : weekdaycolor,
                strokeColor : "#99FF99",
                pointColor : "#99FF99",
                pointStrokeColor : "#fff",
                data: <?php chartarray($cbydaystack); ?>
            },
            {
                fillColor : matecolor,
                strokeColor : matelightcolor,
                pointColor : matelightcolor,
                pointStrokeColor : "#fff",
                data: <?php chartarray($mbydaystack); ?>
            },
        ]
    }

    var myLine = new Chart(document.getElementById("coffeebyweekday").getContext("2d")).Line(lineChartData);
</script>

<?php
include("footer.php");
?>
