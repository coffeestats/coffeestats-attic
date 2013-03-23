<?php
	include('auth/lock.php');
	include("header.php");
?>
		<div class="white-box">
			<h2>Welcome to a whole new version of coffeestats!</h2>
				<p>What does this mean?</p>
                <ul>
                <li> We're now using <a href="http://www.chartjs.org/">Chart.js</a>. This saves your privacy a lot more instead of using Google Charts. Web2.0-hyper-HTML5-power and stuff!</li>
                <li> You can now register <a href="http://www.clubmate.de/">Club-Mate</a> (or any other type of mate) as well!</li>
                <li> New combined graphs for mate and coffee</li> 
                <li> New userfriendly <a href="plusone">update</a> page </li>
                <li> Refactored code for performance reasons</li>
                </ul>
		</div>

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
