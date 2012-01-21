<?php
	include('auth/lock.php');
?>

<?php
	include("header.php"); 
?>

		<div class="white-box">
			<h2>Your charts</h2>
        <?php echo $_SESSION['login_user']; ?>
				<p>Here's your todays statistic</p>
				
		</div>

<?php 
	include("footer.php"); 
?>
