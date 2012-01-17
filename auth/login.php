<?php
	include("config.php");

	session_start();

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		// username and password sent from Form
		$myusername=addslashes($_POST['username']);
		$mypassword=md5(md5(addslashes($_POST['password'])));
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
    		$error="Your username or password seems to be invalid :(";
    		echo("$error");
  		}
	}

	include("../preheader.php");
?>


	<div id="login">
		<div class="white-box">
			<h2>What is coffeestats.org?</h2>
				<p>CoffeeStats is a project written with &hearts; by <strong>F. Baumann</strong> and 
				<strong>H. Winter</strong>.</p>
					<p>If you like coffee and graphs, you're on the right way. You drink this awesome 
					tasting black hot stuff, we count it!</p>
		</div>
	
		<div class="white-box">
			<h2>You want to log in?</h2>
				<form action="" method="post">
					<input type="text" name="username" placeholder="Username" id="login_field_username" />
					<input type="password" name="password" placeholder="Password" id="login_field_password" />
					<input type="submit" name="submit" value="Login" id="login_button_submit" />
		
					<?php
						if (isset($error)) {
							echo("$error");
						}
					?>
				</form>
		</div>
		
		<div class="white-box">
			<h2>About us</h2>	
				<p>F. Baumann and H. Winter are old schoold colleague.</p>
		</div>
		
		<div class="white-box">
			<h2>Chart Example</h2>
			<!-- hier kommt dein chart-example rein -->
		</div>
	</div>

</div> <!-- closing div for #wrapper -->
</body>
</html>

