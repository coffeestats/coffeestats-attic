<?php
include('config.php');
include_once('../includes/common.php');
include_once('../includes/queries.php');

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
        $uid = find_user_uid_by_login($login_session);
        if (!isset($uid)) {
            errorpage(
                'Illegal state',
                'Something unexpected happened.',
                '500 Internal Server Error');
        }
    }

    include_once('../includes/validation.php');

    if (($password = sanitize_password(
        $_POST['password'], $_POST['password2'])) !== FALSE)
    {
        if (set_user_password($uid, hash_password($password))) {
            flash(
                'Your password has been changed successfully!',
                FLASH_SUCCESS);
        }
        redirect_to('../index');
    }
}

include_once('../includes/jsvalidation.php');
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
<?php js_sanitize_password(); ?>
<script type="text/javascript">
$(document).ready(function() {
    $('input#password').focus();

    $('form').submit(function(event) {
        return sanitize_password('input#password', 'input#password2');
    });
});
</script>
<?php
include('../footer.php');
?>
