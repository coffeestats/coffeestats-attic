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
        data.addColumn('string', 'Hour');
        data.addColumn('number', 'Coffees');
        data.addRows([
<?php
                for ( $counter = 0; $counter <= 23; $counter += 1) {
                  $sql="select '".$counter."' as hour, count(cid) as coffees from cs_coffees where DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m-%d') = DATE_FORMAT(cdate,'%Y-%m-%d') and ( DATE_FORMAT(cdate,'%H') = '".$counter."' or DATE_FORMAT(cdate,'%H') = '0".$counter."') and cuid = '".$_SESSION['login_id']."'; ";
                  $result=mysql_query($sql);
                  $row=mysql_fetch_array($result);
                  echo ("\t\t['".$row['hour']."h', ".$row['coffees']."],\n");
                }
                  $sql="select '24' as hour, count(cid) as coffees from cs_coffees where DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m-%d') = DATE_FORMAT(cdate,'%Y-%m-%d') and ( DATE_FORMAT(cdate,'%H') = '24' or DATE_FORMAT(cdate,'%H') = '24') and cuid = '".$_SESSION['login_id']."'; ";
                  $result=mysql_query($sql);
                  $row=mysql_fetch_array($result);
                  echo ("\t\t['".$row['hour']."h', ".$row['coffees']."]");
?>

        ]);

        var options = {
          width: 700, height: 240,
          title: 'Your Coffee today',
          hAxis: {title: 'Hour'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
<div id="chart_div"></div>
<?php
include("footer.php");
?>
