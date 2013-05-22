<?php
include("auth/lock.php");
include_once('includes/queries.php');

$total = total_caffeine();
$todayrows = hourly_caffeine_overall();
$monthrows = daily_caffeine_overall();
$yearrows = monthly_caffeine_overall();
$byhourrows = hourly_caffeine_alltime();
$byweekdayrows = weekdaily_caffeine_alltime();

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
            value: <?php echo($total['coffees']); ?>,
            color: todaycolor
        },
        {
            value: <?php echo($total['mate']); ?>,
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
