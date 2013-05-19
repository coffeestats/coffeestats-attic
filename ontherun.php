<?php
include('auth/config.php');
include_once('includes/common.php');
include_once('includes/validation.php');

if (isset($_GET['t']) && isset($_GET['u'])) {
    if ((($user = sanitize_username($_GET['u'])) !== FALSE) &&
        (($token = sanitize_md5value($_GET['t'], 'Token')) !== FALSE))
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $sql = sprintf(
            "SELECT uid, utoken, ulogin, utimezone FROM cs_users
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
            $timezone = $row['utimezone'];
            if ($timezone == NULL) {
                flash(
                    'Your timezone is not set, you should ' .
                    '<a href="auth/login">login</a> ' .
                    'and define a timezone!',
                    FLASH_WARNING);
            }
        }
        else {
            flash('Invalid token or username', FLASH_ERROR);
            $result->close();
            redirect_to('index');
        }
        $result->close();
    }
    else {
        redirect_to('index');
    }
}
else {
    errorpage('Bad request', 'The request was bad.', '400 Bad Request');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['coffeetime']) && (($coffeetime = sanitize_datetime($_POST['coffeetime'])) !== FALSE)) {
        register_coffee($profileid, $coffeetime, $timezone);
    }
    elseif (isset($_POST['matetime']) && (($matetime = sanitize_datetime($_POST['matetime'])) !== FALSE)) {
        register_mate($profileid, $matetime, $timezone);
    }
    redirect_to($_SERVER['REQUEST_URI']);
}

include_once('includes/jsvalidation.php');
include("header.php");
?>
<div class="white-box">
    <h2>On the run?</h2>
    <center>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="coffeeform" class="otrblockform">
    <input type="submit" value="Coffee!" /><br />
    <input type="hidden" id="coffeetime" name="coffeetime" />
    </form>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="mateform" class="otrblockform">
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
