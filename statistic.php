<?php
include("auth/lock.php");
include("header.php");
include("auth/config.php");
?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Year');
        data.addColumn('number', 'Sales');
        data.addRows([
<?php
        $sql="select DATE_FORMAT(cdate,'%H') as hour, count(cid) as coffees from cs_coffees where DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m-%d') = DATE_FORMAT(cdate,'%Y-%m-%d') and cuid = '".$_SESSION['login_id']."' GROUP BY DATE_FORMAT(cdate,'%h'); ";
        $result=mysql_query($sql);
          while ($row = mysql_fetch_assoc($result)) {
                    echo ("\t\t['".$row['hour']."', '".$row['coffees']."'],\n");
          }
        echo ("\t\t['17','2']");
?>

        ]);

        var options = {
          width: 400, height: 240,
          title: 'Company Performance',
          hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
<div id="chart_div"></div>
<?php
include("footer.php");
?>
