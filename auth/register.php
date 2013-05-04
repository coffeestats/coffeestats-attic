<?php
include('config.php');
include_once('../includes/common.php');
require_once('../lib/recaptchalib.php');
include('../lib/antixss.php');

// Get a key from https://www.google.com/recaptcha/admin/create
// TODO: move this information to a config file (see https://bugs.n0q.org/view.php?id=9)
$publickey = "6LdnPswSAAAAAFSYLEH9f_b0JcPQ2G1VsOHDmJZY";
$privatekey = "6LdnPswSAAAAALLCLsZt2AFTnl5VAcNH5WUDZBvf";

# the response from reCAPTCHA
$resp = null;

# the error code from reCAPTCHA, if any
$error = null;

/**
 * Creates a string of random characters with the given length from the given
 * set of characters.
 */
function random_chars($charset, $charcount) {
    $result = array();
    for ($i=0; $i<$charcount; $i++) {
        $char = $charset[mt_rand(0, strlen($charset) - 1)];
        array_push($result, $char);
    }
    return implode($result);
}

# was there a reCAPTCHA response?
if (isset($_POST['recaptcha_response_field'])) {
    $resp = recaptcha_check_answer(
        $privatekey, $_SERVER["REMOTE_ADDR"],
        $_POST["recaptcha_challenge_field"],
        $_POST["recaptcha_response_field"]);

    if ($resp->is_valid) {
        // TODO: implement better validation including client side validation (see https://bugs.n0q.org/view.php?id=13)
        if (!isset($_POST['Login']) || !ctype_alnum($_POST['Login']) || !isset($_POST['Email']) || !isset($_POST['Password'])) {
            $cerr=2;
        } else {
            $cerr=0;

            $saltchars = './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            // blowfish salt for PHP >= 5.3.7, with 22 random characters, see
            // http://www.php.net/manual/en/function.crypt.php
            $salt = sprintf('$2y$07$%s', random_chars($saltchars, 22));

            $login = AntiXSS::setFilter(mysql_real_escape_string($_POST['Login']), "whitelist", "string");
            $email = AntiXSS::setFilter(mysql_real_escape_string($_POST['Email']), "whitelist", "string");
            $password = crypt(mysql_real_escape_string($_POST['Password']), $salt);
            $otrtoken = md5($password.$login.$salt);
            $forename = AntiXSS::setFilter(mysql_real_escape_string($_POST['Forename']), "whitelist", "string");
            $name = AntiXSS::setFilter(mysql_real_escape_string($_POST['Name']), "whitelist", "string");
            $location = AntiXSS::setFilter(mysql_real_escape_string($_POST['Location']), "whitelist", "string");

            $sql = sprintf(
                "SELECT uid FROM cs_users WHERE ulogin='%s'",
                $login);
            $result = mysql_query($sql);
            if (mysql_errno() !== 0) {
                handle_mysql_error();
            }
            if ($row = mysql_fetch_array($result)) {
                $userexists = TRUE;
            }
            else {
                $userexists = FALSE;
            }
        }

        if (($cerr == 0) && (!isset($userexists) || !$userexists)) {
            $sql = sprintf(
                "INSERT INTO cs_users (
                    ulogin, uemail, ufname, uname, ucryptsum, ujoined,
                    ulocation, upublic, utoken)
                 VALUES (
                    '%s', '%s', '%s', '%s', '%s', NOW(),
                    '%s', 1, '%s')",
                $login, $email, $forename, $name, $password,
                $location, $otrtoken);
            $result = mysql_query($sql);
            if (mysql_errno() !== 0) {
                handle_mysql_error();
            }
            flash("You got it! Yes we hate CAPTCHAs too.", FLASH_SUCCESS);
            redirect_to("../index");
        }
        else {
            flash(
                "Error: Sorry. Username already taken, invalid or you forgot something in General section.",
                FLASH_ERROR);
        }
    }
    else {
        # set the error code so that we can display it
        $error = $resp->error;
    }
}

include('../header.php');
?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
    <div class="white-box">
        <h2>Register</h2>
        <p>Fill these fields with your data, write down what reCAPTCHA says u and click Register!</p>
        <p>
        <b>General</b><br/>
        <input type="text" name="Login" maxlength="20" placeholder="Username" class="register_field_standard" />
        <input type="password" name="Password" maxlength="20" placeholder="Password" class="register_field_standard" />
        <input type="text" name="Email" maxlength="50" placeholder="E-Mail" class="register_field_standard" /></p>
        <p><b>Additional</b><br/>
        <input type="text" name="Forename" maxlength="20" placeholder="Forename" class="register_field_standard" />
        <input type="text" name="Name" maxlength="20" placeholder="Name" class="register_field_standard" />
        <input type="text" name="Location" maxlength="20" placeholder="Location" class="register_field_standard" />
        </p>
    </div> <!-- end of white-box -->

    <div class="white-box"><?php echo recaptcha_get_html($publickey, $error, true); ?></div>

    <div class="white-box">
        <input type="submit" value="Register!" class="register_button_standard" />
    </div>
</form>
<?php
include('../footer.php');
?>
