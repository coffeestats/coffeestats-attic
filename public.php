<?php
include("auth/config.php");
include("preheader.php");
include("lib/antixss.php");

// Parse user
$profileuser=AntiXSS::setFilter($_GET['u'], "whitelist", "string");
$profileuser=mysql_real_escape_string($profileuser);
$sql="SELECT uid, ufname, uname, ulocation FROM cs_users WHERE ulogin='$profileuser' and upublic='yes' ;";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$count=mysql_num_rows($result);
$profileid=$row['uid'];
$profilename=$row['uname'];
$profileforename=$row['ufname'];
$profilelocation=$row['ulocation'];

?>
<div class="white-box">
<?php
if ($count==1) {
    echo ("<h2>".$profileuser."'s Profile</h2>"); 
} else {
  echo "<p>Error: Profile not found</p>";
  echo "<p>go <a href=\"index.php\">back</a></p></div>";
  echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../css/caffeine.css\" />";
  include("footer.php");
  exit; 
}

$sql="SELECT count(cid) as total FROM cs_coffees WHERE cuid='".$profileid."';";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
echo("This is $profileforename $profilename from $profilelocation <br/><br/>");
echo("Coffees total: ".$row['total']."");
?>
</div>
<b></b>

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
	                  			
                                  $sql="SELECT '".$counter."' as hour, count(cid) as coffees 
                                    FROM cs_coffees 
                                    WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m-%d') = DATE_FORMAT(cdate,'%Y-%m-%d') 
                                    AND ( DATE_FORMAT(cdate,'%H') = '".$counter."' OR DATE_FORMAT(cdate,'%H') = '0".$counter."') 
                                    AND cuid = '".$profileid."'; ";
	                  			$result=mysql_query($sql);
	                  			$row=mysql_fetch_array($result);
	                  
	                  			echo ("\t\t['".$row['hour']."', ".$row['coffees']."],\n");
	                			}
	                			
                                  $sql="SELECT '24' as hour, count(cid) as coffees 
                                        FROM  cs_coffees 
                                        WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m-%d') = DATE_FORMAT(cdate,'%Y-%m-%d') 
                                        AND ( DATE_FORMAT(cdate,'%H') = '24' or DATE_FORMAT(cdate,'%H') = '24') 
                                        AND cuid = '".$profileid."'; ";
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
                                $sql="SELECT '".$counter."' AS day, count(cid) AS coffees 
                                      FROM cs_coffees 
                                      WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m') = DATE_FORMAT(cdate,'%Y-%m') 
                                      AND ( DATE_FORMAT(cdate,'%d') = '".$counter."' or DATE_FORMAT(cdate,'%d') = '0".$counter."') 
                                      AND cuid = '".$profileid."'; ";
		                  		$result=mysql_query($sql);
		                  		$row=mysql_fetch_array($result);
		                  		
		                  		echo ("\t\t['".$row['day']."', ".$row['coffees']."],\n");
	                		}
	                  
                                $sql="SELECT '31' AS day, count(cid) AS coffees 
                                      FROM cs_coffees 
                                      WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y-%m') = DATE_FORMAT(cdate,'%Y-%m') 
                                      AND ( DATE_FORMAT(cdate,'%d') = '12' or DATE_FORMAT(cdate,'%d') = '12') 
                                      AND cuid = '".$profileid."'; ";
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
                  $sql="SELECT '".$counter."' AS year, count(cid) AS coffees 
                        FROM cs_coffees 
                        WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y') = DATE_FORMAT(cdate,'%Y') 
                        AND ( DATE_FORMAT(cdate,'%m') = '".$counter."' or DATE_FORMAT(cdate,'%m') = '0".$counter."') 
                        AND cuid = '".$profileid."'; ";
                  $result=mysql_query($sql);
                  $row=mysql_fetch_array($result);
                  echo ("\t\t['".$row['year']."', ".$row['coffees']."],\n");
                }
                  $sql="SELECT '12' AS year, count(cid) AS coffees 
                        FROM cs_coffees 
                        WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%Y') = DATE_FORMAT(cdate,'%Y') 
                        AND ( DATE_FORMAT(cdate,'%m') = '12' or DATE_FORMAT(cdate,'%m') = '12') 
                        AND cuid = '".$profileid."'; ";
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
	                  			
                                  $sql="SELECT '".$counter."' as hour, count(cid) as coffees 
                                    FROM cs_coffees 
                                    WHERE ( DATE_FORMAT(cdate,'%H') = '".$counter."' OR DATE_FORMAT(cdate,'%H') = '0".$counter."') 
                                    AND cuid = '".$profileid."'; ";
	                  			$result=mysql_query($sql);
	                  			$row=mysql_fetch_array($result);
	                  
	                  			echo ("\t\t['".$row['hour']."', ".$row['coffees']."],\n");
	                			}
	                			
                                  $sql="SELECT '24' as hour, count(cid) as coffees 
                                        FROM  cs_coffees 
                                        WHERE ( DATE_FORMAT(cdate,'%H') = '24' or DATE_FORMAT(cdate,'%H') = '24') 
                                        AND cuid = '".$profileid."'; ";
	                  			$result=mysql_query($sql);
	                  			$row=mysql_fetch_array($result);
	                  	
	                  			echo ("\t\t['".$row['hour']."', ".$row['coffees']."]");
							?>

        ]);

        var options = {
          width: 550, height: 240,
          title: 'Your most Coffee by hour (alltime)',
          hAxis: {title: 'Hour'} 
        };

        var chart = new google.visualization.AreaChart(document.getElementById('coffee_hour'));
        chart.draw(data, options);
      }
    </script>


		<div class="white-box">
          <div id="coffee_today"></div>
		</div>
		<div class="white-box">
          <div id="coffee_month"></div>
		</div>
		<div class="white-box">
          <div id="coffee_year"></div>
		</div>
		<div class="white-box">
          <div id="coffee_hour"></div>
		</div>


		<div class="white-box">
		    <p><a href="#">coffeestats.org</a> is a project by <a href="http://noqqe.de">Florian Baumann</a> & <a href="http://savier.n0q.org">Holger Winter</a>. If you like you can follow us on <a href="#">Facebook</a>. We wouldn't be sad if you click "i like", either!</p>
		</div>
</body>
</html>
