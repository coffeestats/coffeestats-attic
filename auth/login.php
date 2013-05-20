<?php
include_once("config.php");
include_once("../includes/common.php");
include_once("../includes/queries.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // username and password sent from Form
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        errorpage('Bad request', 'The request is invalid.', '400 Bad Request');
    }

    include_once('../includes/validation.php');

    if ((($username = sanitize_username($_POST['username'])) !== FALSE) &&
        (($password = sanitize_notempty($_POST['password'], 'Password')) !== FALSE))
    {
        // do some maintenance
        clean_expired_actions();
        clean_inactive_users();

        // authentication
        $validpassword = FALSE;
        $userinfo = find_user_information_for_login($username);
        if ($userinfo !== NULL) {
            // password check
            if (strcmp(
                $userinfo['ucryptsum'],
                crypt($password, $userinfo['ucryptsum'])) === 0) {
                $uid = $userinfo['uid'];
                $validpassword = TRUE;
            }
        }
        if (($validpassword === TRUE) && isset($uid)) {
            $_SESSION['login_user'] = $username;
            $_SESSION['login_id'] = $uid;
            $_SESSION['timezone'] = $userinfo['utimezone'];
            if ($userinfo['utimezone'] === NULL) {
                flash('You have not set your timezone yet.', FLASH_INFO);
                redirect_to('../selecttimezone');
            }
            else {
                redirect_to('../index');
            }
        }

        flash("Your username or password seems to be invalid or you did not activate your account yet :(", FLASH_ERROR);
    }
}

include_once("../includes/jsvalidation.php");
include("../header.php");
?>
<div class="white-box">
    <h2>What is coffeestats.org?</h2>
    <p>You like coffee, mate, graphs and nerdy statistics? Well, we do too!</p>
    <p>It's dead-simple: You enjoy your fix of coffee as usual and we keep
    track of it -- enabling us to present you with awesome statistics about
    your general coffee consumption. Why? Just because, of course!</p>
</div>
<div class="white-box">
    <h2>Login</h2>
    <form action="<?php echo($_SERVER['REQUEST_URI']); ?>" method="post" class="inlineform">
        <input type="text" name="username" id="username" <?php if (isset($username)) { printf('value="%s"', htmlspecialchars($username)); } ?>placeholder="Username" />
        <input type="password" name="password" id="password" placeholder="Password"/>
        <input type="submit" name="submit" value="Login" />
        <p>Forgot your password? <a href="passwordreset">Request a password reset</a>.</p>
        <p>Oh, you don't have an account yet?<br/>
        Simply register one <a href="register">here</a>.</p>
        <?php if (isset($error)) { echo("$error"); } ?>
    </form>
</div>
<div class="white-box">
    <h2>Graphs!</h2>
    Overall Coffee vs. Mate consumption<br><br>
    <canvas id="coffeeexample" width="590" height="240" ></canvas>
</div>
<script type="text/javascript" src="../lib/jquery.min.js"></script>
<?php js_sanitize_username(); ?>
<?php js_sanitize_not_empty(); ?>
<script type="text/javascript" src="../lib/Chart.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('input#username').focus();

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

    new Chart(document.getElementById("coffeeexample").getContext("2d")).Line(lineChartData);

    $('form').submit(function(event) {
        return sanitize_username('input#username')
            && sanitize_not_empty('input#password', 'Password must not be empty');
    });
});
</script>
<?php
include('../footer.php');
?>
