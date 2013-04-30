<?php
	include('auth/lock.php');
	include("header.php");
?>

		<div class="white-box">
			<h2>About coffeestats.org</h2>

                <p>Coffeestats.org was written with the help of two Mac OS X systems and is proudly running on <a href="http://openbsd.org">OpenBSD</a>.
				We want to thank the awesome OpenSource community for all their software and art available under
				the <a href="http://creativecommons.org">CreativeCommons</a> license:</p>
					<ul>
						<li><a href="http://openclipart.org/detail/10764/coffee-cup-by-sl4yerpl-10764">Coffee Cup</a> by sl4yerPL on openclipart.org</li>
						<li><a href="http://subtlepatterns.com/?p=750">Woven</a> by Max Rudberg on subtlepatterns.com </li>
						<li><a href="http://chartjs.org">Chart.js</a></li>
						<li><a href="http://www.dafont.com/harabara.font">Harabara</a> by André Harabara on dafont.com</li>
						<li><a href="http://adamwhitcroft.com/batch/">Batch Iconset</a> by Adam Whitcroft</li>
					</ul>

			<h2>Changelog</h2>
					<ul>
						<li>2013-03-23 New Update page</li>
						<li>2013-03-23 Using Chart.js for Graphs!</li>
						<li>2013-03-23 Added Mate to drinks</li>
						<li>2013-02-15 Added more Ranking stuff onto the explore page</li>
						<li>2013-02-27 Some bugfixes for OTR</li>
						<li>2013-02-15 Max email length resized up to 50 chars</li>
						<li>2013-02-15 Made time for coffee +1 with js not servertime. Better feeling for users who not live in GMT+1</li>
						<li>2013-02-13 Added some awesome icons to navigation and cleaned up interface</li>
                        <li>2012-02-11 Added SSL <a href="https://coffeestats.org">https://coffeestats.org</a></li>
						<li>2012-11-06 Migrated from Debian Squeeze to OpenBSD</li>
						<li>2012-02-11 Added on-the-run Mode</li>
						<li>2012-01-23 Added Overall Stats</li>
					</ul>
		</div>

<?php
	include("footer.php");
?>
