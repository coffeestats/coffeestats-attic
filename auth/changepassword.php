<?php
include('config.php');
include_once(sprintf('%s/../includes/common.php', dirname(__FILE__)));

if (!isset($_SESSION)) {
    session_start();
}
if (isset($_SESSION['reset_password_uid'])) {
    $uid = $_SESSION['reset_password_uid'];
}
else {
    include('lock.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['password']) || !isset($_POST['password2'])) {
        errorpage(
            'Bad Request',
            'The current request contains bad data.',
            '400 Bad Request');
    }

    if (!isset($uid)) {
        $sql = sprintf(
            "SELECT uid FROM cs_users WHERE ulogin='%s'",
            $dbconn->real_escape_string($login_session));
        if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
            handle_mysql_error();
        }
        if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $uid = $row['uid'];
        }
        $result->close();
        if (!isset($uid)) {
            errorpage(
                'Illegal state',
                'Something unexpected happened.',
                '500 Internal Server Error');
        }
    }

    include_once(sprintf('%s/../includes/validation.php', dirname(__FILE__)));

    if (($password = sanitize_password($_POST['password'], $_POST['password2'])) !== FALSE) {
        $sql = sprintf(
            "UPDATE cs_users SET ucryptsum='%s' WHERE uid=%d",
            hash_password($password), $uid);
        if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
            handle_mysql_error();
        }
        flash('Your password has been changed successfully!', FLASH_SUCCESS);
        redirect_to('../index');
    }
}

include('../header.php');
?>
<div class="white-box">
    <h2>Change Your Password</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="inlineform">
    <p>
        <input type="password" name="password" id="password" />
        <input type="password" name="password2" id="password2" />
        <input type="submit" name="Reset my password" />
    </p>
    </form>
</div>
<script type="text/javascript" src="../lib/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('form').submit(function(event) {
        var password = $.trim($('#password')[0].value);
        var repeat = $.trim($('#password2')[0].value);
        var valid = true;

        if (password.length < 8) {
            alert('Password must be at least 8 characters long!');
            $('#password').focus();
            valid = false;
        }
        else if (password != repeat) {
            alert('Passwords must match!');
            $('#password2').focus();
            valid = false;
        }

        return valid;
    });
});
</script>
<?php
include('../footer.php');
?>
