<?php
	include("config.php");

	session_start();

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		// username and password sent from Form
		$myusername=mysql_real_escape_string($_POST['username']);
		$mypassword=md5(md5(mysql_real_escape_string($_POST['password'])));
		$sql="SELECT uid FROM cs_users WHERE ulogin='$myusername' and ucryptsum='$mypassword'";
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result);
		$count=mysql_num_rows($result);
		$id=$row['uid'];

		// if result matched $myusername and $mypassword, table row must be 1 row
	
		if($count==1) {
    		session_register("myusername");
    		$_SESSION['login_user']=$myusername;
    		$_SESSION['login_id']=$id;
    		header("location: ../index.php");
  		}
  
  		else {
    		$error="<center>Your username or password seems to be invalid :(</center>";
  		}
	}

	include("../preheader.php");
?>


	<div id="login">
		<div class="white-box">
			<h2>What is coffeestats.org?</h2>
					<p>If you like coffee and graphs, you're on the right way. You drink this awesome 
					tasting black hot stuff, we count it!</p>
		</div>
	
		<div class="white-box">
			<h2>Login</h2>
				<form action="" method="post">
					<input type="text" name="username" placeholder="Username" id="login_field_username" />
					<input type="password" name="password" placeholder="Password" id="login_field_password" />
					<input type="submit" name="submit" value="Login" id="login_button_submit" />
			        <p>Oh, don't have an account? Simply <a href="register.php">register</a> yourself.</p>
					<?php
						if (isset($error)) {
							echo("$error");
						}
					?>
				</form>
		</div>
		
		
		<div class="white-box">
			<h2>Chart Example</h2>
    			<script type="text/javascript" src="https://www.google.com/jsapi"></script>

   				<script type="text/javascript">
      				google.load("visualization", "1", {packages:["corechart"]});
      				google.setOnLoadCallback(drawChart);
      
      				function drawChart() {
        				var data = new google.visualization.DataTable();
        
        				data.addColumn('string', 'Hour');
        				data.addColumn('number', 'noqqe');
        				data.addColumn('number', 'dreary');
        				data.addRows([
        				['6', 0, 1],
        				['7', 0, 1],
				        ['8', 2, 0],
				        ['9', 1, 0],
				        ['10', 0, 1],
				        ['11', 0, 1],
				        ['12', 3, 1],
				        ['13', 0, 0],
				        ['14', 0, 0],
				        ['15', 0, 2],
				        ['16', 0, 1],
				        ['17', 2, 0],
				        ['18', 0, 1],
				        ]);

            			var options = {
                    		width: 550, height: 240,
                            title: 'Coffees of dreary and noqqe (compared)',
                            hAxis: {
                            	title: 'Hour'}
                            };

            			var chart = new google.visualization.ColumnChart(document.getElementById('coffeeexample'));
            			chart.draw(data, options);
              		}
      			</script>
        
        	<div id="coffeeexample"> <!-- example chart --> </div>    

		</div>
	</div>

		<div class="white-box">
				<p>coffeestats.org is a project written with &hearts; by <strong>F. Baumann</strong> and 
				<strong>H. Winter</strong>.</p>
		</div>
</div> <!-- closing div for #wrapper -->
</body>
</html>

