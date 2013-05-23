<?php
include_once('includes/common.php');
include_once('includes/validation.php');
include_once('includes/queries.php');

if (isset($_GET['t']) && isset($_GET['u'])) {
    if ((($user = sanitize_username($_GET['u'])) !== FALSE) &&
        (($token = sanitize_md5value($_GET['t'], 'Token')) !== FALSE))
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (($userinfo =
            find_user_uid_token_login_and_timezone_by_login_and_token(
                $user, $token)) === NULL)
        {
            flash('Invalid token or username', FLASH_ERROR);
            $result->close();
            redirect_to('index');
        }
        $token = $userinfo['utoken'];
        $user = $userinfo['ulogin'];
        $profileid = $userinfo['uid'];
        $timezone = $userinfo['utimezone'];
        if ($timezone == NULL) {
            flash(
                'Your timezone is not set, you should ' .
                '<a href="auth/login">login</a> ' .
                'and define a timezone!',
                FLASH_WARNING);
        }
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
