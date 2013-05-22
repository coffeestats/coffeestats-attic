<?php
include_once('../includes/common.php');
include_once('../includes/queries.php');
require_once('../lib/recaptchalib.php');

// Get a key from https://www.google.com/recaptcha/admin/create
// The keys are configured in the server environment
$publickey = get_setting(RECAPTCHA_PUBLICKEY);

# the error code from reCAPTCHA, if any
$error = NULL;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // check for existence of expected form fields
    if (!isset($_POST['recaptcha_response_field']) ||
        !isset($_POST['recaptcha_challenge_field']) ||
        !isset($_POST['username']) ||
        !isset($_POST['email']) ||
        !isset($_POST['password']) ||
        !isset($_POST['password2']) ||
        !isset($_POST['firstname']) ||
        !isset($_POST['lastname']) ||
        !isset($_POST['location']))
    {
        errorpage('Bad request', 'The request is invalid.', '400 Bad Request');
    }

    include_once('../includes/validation.php');

    $username = sanitize_username($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = sanitize_password($_POST['password'], $_POST['password2']);
    $firstname = sanitize_string($_POST['firstname'], FALSE);
    $lastname = sanitize_string($_POST['lastname'], FALSE);
    $location = sanitize_string($_POST['location'], FALSE);

    if (($username !== FALSE) && ($email !== FALSE) &&
        ($password !== FALSE) && ($firstname !== FALSE) &&
        ($lastname !== FALSE) && ($location !== FALSE))
    {
        # the response from reCAPTCHA
        $privatekey = get_setting(RECAPTCHA_PRIVATEKEY);
        $resp = recaptcha_check_answer(
            $privatekey, $_SERVER["REMOTE_ADDR"],
            $_POST["recaptcha_challenge_field"],
            $_POST["recaptcha_response_field"]);

        if (!$resp->is_valid) {
            $error = $resp->error;
        }
        else {
            $userexists = find_user_exist_for_login_or_email($username, $email);

            if ($userexists) {
                flash("Error: Sorry. Username already taken.", FLASH_ERROR);
            }
            else {
                $password = hash_password($password);
                $otrtoken = md5($password . $username);

                create_user(
                    $username, $email, $firstname, $lastname, $password,
                    $location, $otrtoken);
                flash("You got it! Yes we hate CAPTCHAs too.", FLASH_SUCCESS);
                send_mail_activation_link($email);
                flash(
                    "We have sent you an email with a link to activate " .
                    "your account.", FLASH_INFO);
                redirect_to("../index");
            }
        }
    }
}

include('../includes/jsvalidation.php');
include('../header.php');
?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <div class="white-box">
        <h2>Register</h2>
        <p>Fill these fields with your data, write down what reCAPTCHA says u and click Register!</p>
        <p>
        <b>General</b><br/>
        <input type="text" name="username" id="username" maxlength="30" placeholder="Username" class="register_field_standard" <?php if (isset($username)) { printf('value="%s"', htmlspecialchars($username)); } ?>/>
        <input type="password" name="password" id="password" maxlength="20" placeholder="Password" class="register_field_standard" />
        <input type="password" name="password2" id="password2" placeholder="Repeat" class="register_field_standard" />
        <input type="text" name="email" id="email" maxlength="128" placeholder="E-Mail" class="register_field_standard" <?php if (isset($email)) { printf('value="%s"', htmlspecialchars($email)); } ?>/></p>
        <p><b>Additional</b><br/>
        <input type="text" name="firstname" id="firstname" maxlength="20" placeholder="First name" class="register_field_standard" <?php if (isset($firstname)) { printf('value="%s"', htmlspecialchars($firstname)); } ?>/>
        <input type="text" name="lastname" id="lastname" maxlength="20" placeholder="Last name" class="register_field_standard" <?php if (isset($lastname)) { printf('value="%s"', htmlspecialchars($lastname)); } ?>/>
        <input type="text" name="location" id="location" maxlength="20" placeholder="Location" class="register_field_standard" <?php if (isset($location)) { printf('value="%s"', htmlspecialchars($location)); } ?>/>
        </p>
    </div> <!-- end of white-box -->

    <div class="white-box"><?php echo recaptcha_get_html($publickey, $error, true); ?></div>

    <div class="white-box">
        <input type="submit" value="Register!" class="register_button_standard" />
    </div>
</form>
<script type="text/javascript" src="../lib/jquery.min.js"></script>
<?php
js_sanitize_username();
js_sanitize_password();
js_sanitize_email();
js_sanitize_string();
?>
<script type="text/javascript">
$(document).ready(function() {
    $('input#username').focus();

    $('form').submit(function(event) {
        return sanitize_username('input#username')
            && sanitize_password('input#password', 'input#password2')
            && sanitize_email('input#email')
            && sanitize_string('input#firstname', false, 'Firstname')
            && sanitize_string('input#lastname', false, 'Lastname')
            && sanitize_string('input#location', false, 'Location');
    });
});
</script>
<?php
include('../footer.php');
?>
