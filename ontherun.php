<?php
include('auth/config.php');
include('lib/antixss.php');
include_once('includes/common.php');
include_once('includes/validation.php');
include_once('includes/jsvalidation.php');

if (isset($_GET['t']) && isset($_GET['u'])) {
    $token = AntiXSS::setFilter($_GET['t'], 'whitelist', 'string');
    $user = AntiXSS::setFilter($_GET['u'], 'whitelist', 'string');
    $sql = sprintf(
        "SELECT uid, utoken, ulogin FROM cs_users
         WHERE ulogin='%s' AND utoken='%s'",
         $dbconn->real_escape_string($user),
         $dbconn->real_escape_string($token));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $token = $row['utoken'];
        $user = $row['ulogin'];
        $profileid = $row['uid'];
    }
    $result->close();
}

if (!isset($token) || !isset($user)) {
    redirect_to('auth/login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['coffeetime']) && (($coffeetime = sanitize_datetime($_POST['coffeetime'])) !== FALSE)) {
        register_coffee($profileid, $coffeetime);
    }
    elseif (isset($_POST['matetime']) && (($matetime = sanitize_datetime($_POST['matetime'])) !== FALSE)) {
        register_mate($profileid, $matetime);
    }
}

include("header.php");
?>
<div class="white-box">
    <h2>On the run?</h2>
    <center>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="coffeeform" class="blockform">
    <input type="submit" value="Coffee!" /><br />
    <input type="hidden" id="coffeetime" name="coffeetime" />
    </form>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="mateform" class="blockform">
    <input type="submit" value="Mate!" /><br />
    <input type="hidden" id="matetime" name="matetime" />
    </form>
    </center>
</div>
<script type="text/javascript" src="lib/jquery.min.js"></script>
<?php js_sanitize_datetime(); ?>
<script type="text/javascript">
$(document).ready(function() {
    $('#coffeeform').submit(function(event) {
        return sanitize_datetime('input#coffeetime');
    });
    $('#mateform').submit(function(event) {
        return sanitize_datetime('input#matetime');
    });
});
</script>
<?php
include('footer.php');
?>
