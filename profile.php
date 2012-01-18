<?php
include("auth/lock.php");
include("auth/config.php");
include("header.php");

$profileuser=htmlspecialchars($_GET["u"]);
$sql="SELECT uid FROM cs_users WHERE ulogin='$profileuser'";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$profileid=mysql_real_escape_string(addslashes($row['uid']));
echo $profileid;
?>
<b>Statistic</b><br/>
<br/>



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
                  $sql="select '".$counter."' as hour, count(cid) as coffees from cs_coffees where DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m-%d') = DATE_FORMAT(cdate,'%Y-%m-%d') and ( DATE_FORMAT(cdate,'%H') = '".$counter."' or DATE_FORMAT(cdate,'%H') = '0".$counter."') and cuid = '".$profileid."'; ";
                  $result=mysql_query($sql);
                  $row=mysql_fetch_array($result);
                  echo ("\t\t['".$row['hour']."', ".$row['coffees']."],\n");
                }
                  $sql="select '24' as hour, count(cid) as coffees from cs_coffees where DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m-%d') = DATE_FORMAT(cdate,'%Y-%m-%d') and ( DATE_FORMAT(cdate,'%H') = '24' or DATE_FORMAT(cdate,'%H') = '24') and cuid = '".$profileid."'; ";
                  $result=mysql_query($sql);
                  $row=mysql_fetch_array($result);
                  echo ("\t\t['".$row['hour']."', ".$row['coffees']."]");
?>

        ]);

        var options = {
          width: 550, height: 240,
          title: 'Your coffees today',
          hAxis: {title: 'Hour'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('coffee_today'));
        chart.draw(data, options);
      }
    </script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Month');
        data.addColumn('number', 'Coffees');
        data.addRows([
<?php
                for ( $counter = 1; $counter <= 30; $counter += 1) {
                  $sql="select '".$counter."' as day, count(cid) as coffees from cs_coffees where DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m') = DATE_FORMAT(cdate,'%Y-%m') and ( DATE_FORMAT(cdate,'%d') = '".$counter."' or DATE_FORMAT(cdate,'%d') = '0".$counter."') and cuid = '".$profileid."'; ";
                  $result=mysql_query($sql);
                  $row=mysql_fetch_array($result);
                  echo ("\t\t['".$row['day']."', ".$row['coffees']."],\n");
                }
                  $sql="select '31' as day, count(cid) as coffees from cs_coffees where DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m') = DATE_FORMAT(cdate,'%Y-%m') and ( DATE_FORMAT(cdate,'%d') = '12' or DATE_FORMAT(cdate,'%d') = '12') and cuid = '".$profileid."'; ";
                  $result=mysql_query($sql);
                  $row=mysql_fetch_array($result);
                  echo ("\t\t['".$row['day']."', ".$row['coffees']."]");
?>

        ]);

        var options = {
          width: 550, height: 240,
          title: 'Your coffees this month',
          hAxis: {title: 'Day'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('coffee_month'));
        chart.draw(data, options);
      }
    </script>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Day');
        data.addColumn('number', 'Coffees');
        data.addRows([
<?php
                for ( $counter = 1; $counter <= 11; $counter += 1) {
                  $sql="select '".$counter."' as year, count(cid) as coffees from cs_coffees where DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y') = DATE_FORMAT(cdate,'%Y') and ( DATE_FORMAT(cdate,'%m') = '".$counter."' or DATE_FORMAT(cdate,'%m') = '0".$counter."') and cuid = '".$profileid."'; ";
                  $result=mysql_query($sql);
                  $row=mysql_fetch_array($result);
                  echo ("\t\t['".$row['year']."', ".$row['coffees']."],\n");
                }
                  $sql="select '12' as year, count(cid) as coffees from cs_coffees where DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y') = DATE_FORMAT(cdate,'%Y') and ( DATE_FORMAT(cdate,'%m') = '12' or DATE_FORMAT(cdate,'%m') = '12') and cuid = '".$profileid."'; ";
                  $result=mysql_query($sql);
                  $row=mysql_fetch_array($result);
                  echo ("\t\t['".$row['year']."', ".$row['coffees']."]");
?>

        ]);

        var options = {
          width: 550, height: 240,
          title: 'Your coffees this year',
          hAxis: {title: 'Year'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('coffee_year'));
        chart.draw(data, options);
      }
    </script>



<div id="coffee_today"></div><br/>
<div id="coffee_month"></div><br/>
<div id="coffee_year"></div>
<?php
include("footer.php");
?>
<br/>
