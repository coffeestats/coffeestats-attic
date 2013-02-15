<?php
	include('auth/lock.php');
	include("header.php");
?>

		<div class="white-box">
			<h2>Whats up?</h2>
				<p>You like coffee, graphs and nerdy statistics? Well, we do too!</p>
				<p>If you had a coffee today, or are about to drink one, simply let us know <a href="plusone">here</a> and we'll keep track of it.
                You can also <a href="explore">explore</a> a bit and check out the other user's statistics!</p>
		</div>

		<div class="white-box">
            <h2>You're not always on a Workstation?</h2>
            <p>Register a coffee on-the-run! Get your on-the-run url on your <a href="profile?u=<?php echo $_SESSION['login_user']; ?>">profile page</a>.
                   Simply bookmark the url on your mobile device and you will never forget a coffee =)</p>
		</div>

<?php
	include("footer.php");
?>
