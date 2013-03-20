<?php
include("auth/lock.php");
include("auth/config.php");
include("header.php");
include("lib/antixss.php");

$ctodaystack = array(); 
$htodaystack = array(); 
for ( $counter = 1; $counter <= 24; $counter += 1) {
  $sql="SELECT count(cid) as coffees, '".$counter."' as hour
    FROM cs_coffees 
    WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m-%d') = DATE_FORMAT(cdate,'%Y-%m-%d') 
    AND ( DATE_FORMAT(cdate,'%H') = '".$counter."' OR DATE_FORMAT(cdate,'%H') = '0".$counter."'); ";
  $result=mysql_query($sql);
  $row = mysql_fetch_array($result);
  array_push($ctodaystack, $row['coffees']);
  array_push($htodaystack, $row['hour']);
}

$cmonthstack = array();
$dmonthstack = array();
for ( $counter = 1; $counter <= 30; $counter += 1) {
  $sql="SELECT '".$counter."' AS day, count(cid) AS coffees 
        FROM cs_coffees 
        WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m') = DATE_FORMAT(cdate,'%Y-%m') 
        AND ( DATE_FORMAT(cdate,'%d') = '".$counter."' or DATE_FORMAT(cdate,'%d') = '0".$counter."'); ";
  $result=mysql_query($sql);
  $row=mysql_fetch_array($result);
  array_push($cmonthstack, $row['coffees']);
  array_push($dmonthstack, $row['day']);
}

$cyearstack = array();
$myearstack = array();
for ( $counter = 1; $counter <= 12; $counter += 1) {
  $sql="SELECT '".$counter."' AS month, count(cid) AS coffees 
        FROM cs_coffees 
        WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y') = DATE_FORMAT(cdate,'%Y') 
        AND ( DATE_FORMAT(cdate,'%m') = '".$counter."' or DATE_FORMAT(cdate,'%m') = '0".$counter."'); ";
  $result=mysql_query($sql);
  $row=mysql_fetch_array($result);
  array_push($cyearstack, $row['coffees']);
  array_push($myearstack, $row['month']);
}

$cbyhourstack = array();
$hbyhourstack = array();
for ( $counter = 0; $counter <= 24; $counter += 1) {
    $sql="SELECT '".$counter."' as hour, count(cid) as coffees 
          FROM cs_coffees 
          WHERE DATE_FORMAT(cdate,'%H') = '".$counter."' OR DATE_FORMAT(cdate,'%H') = '0".$counter."'; ";
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
array_push($cbyhourstack, $row['coffees']);
array_push($hbyhourstack, $row['hour']);
}

$cbydaystack = array();
$hbydaystack = array();
$sql="SELECT DATE_FORMAT(cdate, '%a') as day, count(cid) as coffees 
      FROM cs_coffees 
      GROUP BY day
      ORDER BY DATE_FORMAT(cdate, '%w'); ";
$result=mysql_query($sql);
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
  array_push($cbydaystack, $row[1]);
  array_push($hbydaystack, $row[0]);
}

?>
<div class="white-box">
  <h2>Overall Statistics</h2>

  <p>We love stats. On overall statistics we started making awesome graphs examining the daily coffee
     consumption of anyone using coffeestats.org. There are different approaches to visualize this. At least a few of them are listed below.</p>
</div>

		<div class="white-box">
          <h2>Coffees today</h2>
          <canvas id="coffeetoday" width="590" height="240" ></canvas>
		</div>
		<div class="white-box">
          <h2>Coffees this month</h2>
          <canvas id="coffeemonth" width="590" height="240" ></canvas>
		</div>
		<div class="white-box">
          <h2>Coffees this year</h2>
          <canvas id="coffeeyear" width="590" height="240" ></canvas>
		</div>
		<div class="white-box">
          <h2>Coffees by hour (overall)</h2>
          <canvas id="coffeebyhour" width="590" height="240" ></canvas>
		</div>
		<div class="white-box">
          <h2>Coffees by weekday (overall)</h2>
          <canvas id="coffeebyweekday" width="590" height="240" ></canvas>
		</div>

 <script src="./lib/Chart.min.js"></script>

  <script>
  var todaycolor = "#E64545"
  var monthcolor = "#FF9900"
  var yearcolor = "#3399FF"
  var hourcolor = "#FF6666"
  var weekdaycolor = "#A3CC52"
  </script>

  <script>

    var barChartData = {
    <?php
    echo ("labels : [");
    foreach ($htodaystack as &$value) {
      echo ($value.",");
    }
    unset($value);
    echo ("],\n");
    ?>
      datasets : [
        {
          fillColor : todaycolor,
          strokeColor : todaycolor,
          <?php
          echo ("data : [");
          foreach ($ctodaystack as &$value) {
            echo ($value.",");
          }
          unset($value);
          echo ("]\n");
          ?>
        },
      ]

    }

    var myLine = new Chart(document.getElementById("coffeetoday").getContext("2d")).Bar(barChartData);

  </script>

  <script>
    var lineChartData = {
          <?php
          echo ("labels : [");
          foreach ($dmonthstack as &$value) {
            echo ($value.",");
          }
          unset($value);
          echo ("],\n");
          ?>

      datasets : [
        {
          fillColor : monthcolor, 
          strokeColor : "#FFB84D",
          pointColor : "#FFB84D",
          pointStrokeColor : "#fff",
          <?php
          echo ("data : [");
          foreach ($cmonthstack as &$value) {
            echo ($value.",");
          }
          unset($value);
          echo ("]\n");
          ?>

        },
      ]
    }

  var myLine = new Chart(document.getElementById("coffeemonth").getContext("2d")).Line(lineChartData);
 </script>


  <script>

    var barChartData = {
    <?php
    echo ("labels : [");
    foreach ($myearstack as &$value) {
      echo ($value.",");
    }
    unset($value);
    echo ("],\n");
    ?>
      datasets : [
        {
          fillColor : yearcolor,
          strokeColor : yearcolor,
          <?php
          echo ("data : [");
          foreach ($cyearstack as &$value) {
            echo ($value.",");
          }
          unset($value);
          echo ("]\n");
          ?>
        },
      ]

    }

    var myLine = new Chart(document.getElementById("coffeeyear").getContext("2d")).Bar(barChartData);
  </script>

  <script>
    var lineChartData = {
          <?php
          echo ("labels : [");
          foreach ($hbyhourstack as &$value) {
            echo ($value.",");
          }
          unset($value);
          echo ("],\n");
          ?>

      datasets : [
        {
          fillColor : hourcolor, 
          strokeColor : "#FF9999",
          pointColor : "#FF9999",
          pointStrokeColor : "#fff",
          <?php
          echo ("data : [");
          foreach ($cbyhourstack as &$value) {
            echo ($value.",");
          }
          unset($value);
          echo ("]\n");
          ?>

        },
      ]
    }

  var myLine = new Chart(document.getElementById("coffeebyhour").getContext("2d")).Line(lineChartData);
 </script>

 <script>
    var lineChartData = {
          <?php
          echo ("labels : [");
          foreach ($hbydaystack as &$value) {
            echo ('"'.$value.'",');
          }
          unset($value);
          echo ("],\n");
          ?>

      datasets : [
        {
          fillColor : hourcolor, 
          strokeColor : "#FF9999",
          pointColor : "#FF9999",
          pointStrokeColor : "#fff",
          <?php
          echo ("data : [");
          foreach ($cbyhourstack as &$value) {
            echo ($value.",");
          }
          unset($value);
          echo ("]\n");
          ?>

        },
      ]
    }

  var myLine = new Chart(document.getElementById("coffeebyhour").getContext("2d")).Line(lineChartData);
 </script>

 <script>
    var lineChartData = {
          <?php
          echo ("labels : [");
          foreach ($hbydaystack as &$value) {
            echo ('"'.$value.'",');
          }
          unset($value);
          echo ("],\n");
          ?>

      datasets : [
        {
          fillColor : weekdaycolor, 
          strokeColor : "#99FF99",
          pointColor : "#99FF99",
          pointStrokeColor : "#fff",
          <?php
          echo ("data : [");
          foreach ($cbydaystack as &$value) {
            echo ($value.",");
          }
          unset($value);
          echo ("]\n");
          ?>

        },
      ]
    }

  var myLine = new Chart(document.getElementById("coffeebyweekday").getContext("2d")).Line(lineChartData);
 </script>

<?php
	include("footer.php");
?>
