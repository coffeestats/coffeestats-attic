<?php
/*
 * Password reset request page.
 */
include_once('../includes/common.php');
include_once('../includes/queries.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email'])) {
        errorpage('Bad request', 'The request is invalid.', '400 Bad Request');
    }

    include_once('../includes/validation.php');

    if (($email = sanitize_email($_POST['email'])) !== FALSE) {
        send_reset_password_link($email);
        flash('We sent an email with a password reset link if any of our users has an account with the given email address.', FLASH_INFO);
        redirect_to('login');
    }
    redirect_to($_SERVER['REQUEST_URI']);
}

if (!isset($_SESSION)) {
    session_start();
}
include_once('../includes/jsvalidation.php');
include('../header.php');
?>
<div class="white-box fullWidth">
    <h2>Request a password reset</h2>
    <p>Please enter the email address that you used when you registered. We will send you an email with a link that you can use to reset your password.</p>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="inlineform">
        <input type="email" required pattern="[A-Za-z0-9._%+-]+@[^@]+" name="email" id="email" autofocus class="left" />
        <input type="submit" name="submit" value="Send me a reset link!" class="left" />
    </form>
    <p></p>
</div>
<script type="text/javascript" src="../lib/jquery.min.js"></script>
<?php js_sanitize_email(); ?>
<script type="text/javascript">
$(document).ready(function() {
    $('input#email').bind('invalid', emailfieldvalidation);

    if (!("autofocus" in document.createElement('input'))) {
        $("input#email").focus();
    }

    $('form').submit(function(event) {
        return sanitize_email('input#email');
    });
});
</script>
<?php
include('../footer.php');
?>
