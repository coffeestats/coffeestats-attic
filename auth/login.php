<?php
include_once("config.php");
include_once("../includes/common.php");

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // username and password sent from Form
    // TODO: proper input validation (see https://bugs.n0q.org/view.php?id=13)

    // do some maintenance
    clean_expired_actions();
    clean_inactive_users();

    // authentication
    $validpassword = FALSE;
    $myusername=$dbconn->real_escape_string($_POST['username']);
    $sql = sprintf(
        "SELECT uid, ucryptsum FROM cs_users WHERE ulogin='%s' AND uactive=1",
        $myusername);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        // password check
        if (strcmp($row['ucryptsum'], crypt($dbconn->real_escape_string($_POST['password']), $row['ucryptsum'])) === 0) {
            $uid = $row['uid'];
            $validpassword = TRUE;
        }
    }
    $result->close();
    if (($validpassword === TRUE) && isset($uid)) {
        $_SESSION['login_user'] = $myusername;
        $_SESSION['login_id'] = $uid;
        redirect_to('../index');
    }

    flash("Your username or password seems to be invalid or you did not activate your account yet :(", FLASH_ERROR);
}

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
        <input type="text" name="username" placeholder="Username" />
        <input type="password" name="password" placeholder="Password"/>
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
<script type="text/javascript" src="../lib/Chart.min.js"></script>
<script type="text/javascript">
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
</script>
<?php
include('../footer.php');
?>
