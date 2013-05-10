<?php
/*
 * Password reset request page.
 */
include('config.php');
include_once('../includes/common.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email'])) {
        errorpage('Bad request', 'The request is invalid.', '400 Bad Request');
    }
    send_reset_password_link($_POST['email']);
    flash('We sent an email with a password reset link if any of our users has an account with the given email address.', FLASH_INFO);
    redirect_to('login');
}

include('../header.php');
?>
<div class="white-box">
    <h2>Request a password reset</h2>
    <p>Please enter the email address that you used when you registered. We will send you an email with a link that you can use to reset your password.</p>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="inlineform">
        <p>
        <input type="text" name="email" />
        <input type="submit" name="submit" value="Send me a reset link!" />
        </p>
    </form>
</div>
<?php
include('../footer.php');
?>
