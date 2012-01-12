<?php
include('auth/lock.php');
?>

<?php include("header.php"); ?>

	Welcome, <strong><?php echo $login_session; ?></strong>!<br />What would you like to do?
	<a href="auth/logout.php">Logout</a></p>

<?php include("footer.php"); ?>
