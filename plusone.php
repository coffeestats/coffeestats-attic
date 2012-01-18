<?php
include('auth/lock.php');
include("header.php"); 
?>

		<div class="white-box">
			<h2>Ahhh, another one, huh?</h2>
				<p>We know, you can't get your fingers away from coffee. Update your consume by +1!</p>
				
				<form action="" method="post">
					<input class="imadecoffee" type="submit" value="Right! And It tastes f*cking awesome" id="coffee_plus_one" /><br />
				</form>
				
				<?php 
					if($_SERVER["REQUEST_METHOD"] == "POST") {
						include('auth/config.php');
						
						$sql="INSERT INTO cs_coffees VALUES ('','".$_SESSION['login_id']."', NOW() ); ";
						$result=mysql_query($sql);
						
						echo("<p>Your coffee has been registered!</p>");
					}
				?>
		</div>

<?php
	include('footer.php');
?>

