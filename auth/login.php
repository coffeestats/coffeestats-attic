<?php
include("config.php");
include("../includes/common.php");

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // username and password sent from Form
    $myusername=mysql_real_escape_string($_POST['username']);
    $validpassword = FALSE;
    $sql = sprintf(
        "SELECT uid, ucryptsum FROM cs_users WHERE ulogin='%s'",
        $myusername);
    $result = mysql_query($sql);
    if (mysql_errno() !== 0) {
        handle_mysql_error();
    }
    if ($row = mysql_fetch_array($result)) {
        // password check
        if (strcmp($row['ucryptsum'], crypt(mysql_real_escape_string($_POST['password']), $row['ucryptsum'])) === 0) {
            $uid = $row['uid'];
            $validpassword = TRUE;
        }
    }
    if (($validpassword === TRUE) && isset($uid)) {
        $_SESSION['login_user'] = $myusername;
        $_SESSION['login_id'] = $uid;
        include('../includes/common.php');
        redirect_to('../index');
    }

    $error = "<center>Your username or password seems to be invalid :(</center>";
}

include("../header.php");
?>
<div id="login">
    <div class="white-box">
        <h2>What is coffeestats.org?</h2>
        <p>You like coffee, mate, graphs and nerdy statistics? Well, we do too!</p>
        <p>It's dead-simple: You enjoy your fix of coffee as usual and we keep
        track of it -- enabling us to present you with awesome statistics about
        your general coffee consumption. Why? Just because, of course!</p>
    </div>
    <div class="white-box">
        <h2>Login</h2>
        <form action="<?php echo($_SERVER['REQUEST_URI']); ?>" method="post">
            <input type="text" name="username" placeholder="Username" id="login_field_username" />
            <input type="password" name="password" placeholder="Password" id="login_field_password" />
            <input type="submit" name="submit" value="Login" id="login_button_submit" />
            <p>Oh, you don't have an account yet?<br/>
            Simply register one <a href="register">here</a>.</p>
            <?php if (isset($error)) { echo("$error"); } ?>
        </form>
    </div>
    <div class="white-box">
        <h2>Graphs!</h2>
        Overall Coffee vs. Mate consumption<br><br>
        <canvas id="coffeeexample" width="590" height="240" ></canvas>
        <script src="../lib/Chart.min.js"></script>
        <script>
            var lineChartData = {
                labels: ["Sun","Mon","Tue","Wed","Thu","Fri","Sat",],
                datasets: [
                    {
                        fillColor: "#FF9900",
                        strokeColor: "#FFB84D",
                        pointColor: "#FFB84D",
                        pointStrokeColor: "#fff",
                        data: [40,26,180,72,102,60,30,14,]
                    },
                    {
                        fillColor:  "#E64545",
                        strokeColor: "#FF9999",
                        pointColor: "#FF9999",
                        pointStrokeColor: "#fff",
                        data: [101,3,87,32,12,80,17,14,]
                    },
                ]
            }

            var myLine = new Chart(document.getElementById("coffeeexample").getContext("2d")).Line(lineChartData);
        </script>
    </div>
</div>

<!-- Piwik -->
<script type="text/javascript">
    var pkBaseURL = (("https:" == document.location.protocol) ? "https://piwik.n0q.org/" : "http://piwik.n0q.org/");
    document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));

    try {
        var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 6);
        piwikTracker.trackPageView();
        piwikTracker.enableLinkTracking();
    } catch( err ) {}
</script>
<noscript><p><img src="http://piwik.n0q.org/piwik.php?idsite=6" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->

<?php
include('../footer.php');
?>
