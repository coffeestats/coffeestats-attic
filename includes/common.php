<?php
if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    header('Status: 301 Moved Permanently');
    header('Location: ../index');
    exit();
}

include_once(sprintf('%s/queries.php', dirname(__FILE__)));

/*
 * Provide commonly usable code to implement DRY principle.
 */

define('FLASH_INFO', "info");
define('FLASH_SUCCESS', "success");
define('FLASH_ERROR', "error");
define('FLASH_WARNING', "warning");

/*
 * Constants for setting names.
 */
define('MAIL_FROM_ADDRESS', 'COFFEESTATS_MAIL_FROM_ADDRESS');

define('MYSQL_DATABASE', 'COFFEESTATS_MYSQL_DATABASE');
define('MYSQL_HOSTNAME', 'COFFEESTATS_MYSQL_HOSTNAME');
define('MYSQL_PASSWORD', 'COFFEESTATS_MYSQL_PASSWORD');
define('MYSQL_USER', 'COFFEESTATS_MYSQL_USER');

define('PIWIK_HOST', 'COFFEESTATS_PIWIK_HOST');
define('PIWIK_SITE_ID', 'COFFEESTATS_PIWIK_SITEID');

define('RECAPTCHA_PRIVATEKEY', 'COFFEESTATS_RECAPTCHA_PRIVATEKEY');
define('RECAPTCHA_PUBLICKEY', 'COFFEESTATS_RECAPTCHA_PUBLICKEY');

define('SITE_SECRET', 'COFFEESTATS_SITE_SECRET');
define('SITE_NAME', 'COFFEESTATS_SITE_NAME');
define('SITE_ADMINMAIL', 'COFFEESTATS_SITE_ADMINMAIL');

$ENTRY_TYPES = array(
    0 => 'coffee',
    1 => 'mate',
);

/**
 * Store a flash message in the flash message stack.
 */
function flash($message, $level=FLASH_INFO) {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = array();
    }
    array_push($_SESSION['flash'], array($level, $message));
}

/**
 * Returns TRUE if there are messages in the flash message stack.
 */
function peek_flash() {
    return (
        isset($_SESSION) &&
        isset($_SESSION['flash']) &&
        (count($_SESSION['flash']) > 0));
}

/**
 * Get the first message from the flash message stack.
 */
function pop_flash() {
    $message = NULL;
    if (peek_flash()) {
        $message = array_shift($_SESSION['flash']);
    }
    return $message;
}

/**
 * Redirect to given URL.
 */
function redirect_to($url, $permanent=FALSE) {
    if ($permanent) {
        header("Status: 301 Redirect Permanently");
    }
    header(sprintf("Location: %s", $url));
    exit();
}

/**
 * Generate an error page with a title, a text and an optional HTTP status
 * code.
 */
function errorpage($title, $text, $http_status=NULL) {
    if ($http_status !== NULL) {
        header(sprintf('Status: %s', $http_status));
    }
    include(sprintf('%s/../header.php', dirname(__FILE__)));
?>
<div class="white-box">
    <h2><?php echo $title; ?></h2>
    <p><?php echo $text ?></p>
</div>
<?php
    include(sprintf('%s/../footer.php', dirname(__FILE__)));
    exit();
}

/**
 * Get a configuration setting from the environment and create an appropriate
 * error page if it is missing.
 */
function get_setting($setting_name, $mandatory=TRUE) {
    if (!isset($_SERVER[$setting_name])) {
        if ($mandatory) {
            errorpage(
                "Wrong configuration",
                sprintf(
                    "The mandatory configuration setting " .
                    "<strong>%s</strong> is not set for this coffeestats " .
                    "instance. The administrator of the site has to " .
                    "configure it.",
                    $setting_name),
                "503 Service Unavailable");
        }
        else {
            return NULL;
        }
    }
    return $_SERVER[$setting_name];
}

/**
 * Generates the base URL based on information of the current request's
 * environment.
 */
function baseurl() {
    $protocol = 'http';
    if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'], 'off') != 0)) {
        $protocol = 'https';
    }
    $appendport = (
        (($protocol === 'https') && ($_SERVER['SERVER_PORT'] !== '443')) ||
        (($protocol === 'http') && ($_SERVER['SERVER_PORT'] !== '80'))
    );
    return sprintf(
        "%s://%s%s",
        $protocol, $_SERVER['SERVER_NAME'],
        $appendport ? ":" . $_SERVER['SERVER_PORT'] : "");
}

/**
 * Return a user's public profile URL.
 */
function public_url($username) {
    return sprintf("%s/profile?u=%s", baseurl(), urlencode($username));
}

/**
 * Return a user's on-the-run URL.
 */
function on_the_run_url($profileuser, $profiletoken) {
    return sprintf(
        "%s/ontherun?u=%s&t=%s",
        baseurl(), urlencode($profileuser), urlencode($profiletoken));
}

/**
 * Generate HTML code for a user's public profile link.
 */
function profilelink($username) {
    return sprintf(
        '<a href="%s">%s</a>',
        public_url($username), htmlspecialchars($username));
}

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

/**
 * Hash a password for database storage.
 */
function hash_password($password) {
    $saltchars = './0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // blowfish salt for PHP >= 5.3.7, with 22 random characters, see
    // http://www.php.net/manual/en/function.crypt.php
    $salt = sprintf('$2y$07$%s', random_chars($saltchars, 22));
    return crypt($password, $salt);
}

/**
 * Send a system mail (potentialy a multipart mail) to a given mail address.
 *
 * Partly ripoff from http://www.php.net/manual/de/function.mail.php#105661
 * for sending multiple attachments via mail
 *
 * The $files array must have an array with the fields 'content-type', 
 * 'description', 'realfile', 'filename' for each file.
 */
function send_system_mail($to, $subject, $body, &$files=NULL) {
    $sendermail = get_setting(MAIL_FROM_ADDRESS);
    $from = sprintf(
        "From: %s <%s>",
        get_setting(SITE_NAME), $sendermail);

    $headers = array($from);

    if (($files !== NULL) && (count($files) > 0)) {
        $mime_boundary = sprintf(
            "==Multipart_Boundary_x(%sx",
            md5(time() + mt_rand()));
        array_push($headers, "MIME-Version: 1.0");
        array_push($headers, sprintf(
            'Content-Type: multipart/mixed; boundary="%s"',
            $mime_boundary));

        $message = sprintf("--%s\r\n", $mime_boundary);
        $message .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
        $message .= "Content-Transfer-Encoding: quoted-printable\r\n\r\n";
        $message .= quoted_printable_encode($body);
        $message .= "\r\n";

        foreach ($files as $filepart) {
            if (is_file($filepart['realfile'])) {
                $message .= sprintf("--%s\r\n", $mime_boundary);
                $message .= sprintf(
                    "Content-Type: %s; name=\"%s\"\r\n",
                    $filepart['content-type'],
                    $filepart['filename']);
                $message .= sprintf(
                    "Content-Description: %s\r\n", $filepart['description']);
                $message .= sprintf(
                    "Content-Disposition: attachment; filename=\"%s\";".
                    " size=%d\r\n",
                    $filepart['filename'],
                    filesize($filepart['realfile']));
                $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $message .= chunk_split(
                    base64_encode(file_get_contents($filepart['realfile'])));
            }
            else {
                error_log(sprintf(
                    "%s is no file.", $filepart['filename']));
            }
        }
        // $message .= sprintf("--%s\r\n", $mime_boundary);
    }
    else {
        array_push($headers, "Content-Type: text/plain; charset=\"utf-8\"");
        array_push($headers, "Content-Transfer-Encoding: quoted-printable");
        $message = quoted_printable_encode($body);
    }

    $returnpath = "-f" . $sendermail;
    $ok = @mail($to, $subject, $message, implode("\r\n", $headers), $returnpath);

    return $ok;
}


/**
 * Send the caffeine track record mail with the attached files.
 */
function send_caffeine_mail($to, &$files) {
    $subject = "Your caffeine records";
    $body = "Attached is your caffeine track record.";

    return send_system_mail($to, $subject, $body, $files);
}


/**
 * Generates an action code for the cs_actions table.
 */
function generate_actioncode($data) {
    return md5(sprintf("%s%s%s", get_setting(SITE_SECRET), mt_rand(), $data));
}

$ACTION_TYPES = array(
    'activate_mail' => 1,
    'reset_password' => 2,
    'change_email' => 3,
);

/**
 * Create an entry in the cs_actions table.
 */
function create_action_entry($cuid, $action_type, $data) {
    global $ACTION_TYPES;
    if (!array_key_exists($action_type, $ACTION_TYPES)) {
        error_log(sprintf("Invalid action code %s", $action_type));
        return FALSE;
    }
    $actioncode = generate_actioncode($data);
    create_action($cuid, $ACTION_TYPES[$action_type], $actioncode, $data);
    return $actioncode;
}

/**
 * Get the absolute URL for the given action code.
 */
function get_action_url($actioncode) {
    return sprintf('%s/action?code=%s', baseurl(), urlencode($actioncode));
}

/**
 * Fill a mail template.
 */
function fill_mail_template($templatename, $placeholders) {
    $templatefile = sprintf('%s/../templates/%s.txt', dirname(__FILE__), $templatename);
    if (!file_exists($templatefile)) {
        error_log(sprintf(
            'Invalid mail template name %s, file %s does not exist.',
            $templatename, $templatefile));
        errorpage(
            'Error', 'Error, could not create mail.',
            '500 Internal Server Error');
    }
    $keys = array();
    $values = array();
    foreach ($placeholders as $key => $value) {
        array_push($keys, sprintf('@%s@', $key));
        array_push($values, $value);
    }

    return str_replace(
        $keys, $values, file_get_contents($templatefile));
}

/**
 * Sends a mail to activate an account.
 */
function send_mail_activation_link($email) {
    if (($userinfo = find_user_firstname_login_uid_by_email($email)) === NULL)
    {
        errorpage(
            'Invalid data', 'Invalid data was sent',
            '500 Internal Server Error');
    }
    $firstname = $userinfo['ufname'];
    $cuid = $userinfo['uid'];

    $actioncode = create_action_entry($cuid, 'activate_mail', $email);
    if ($actioncode === FALSE) {
        errorpage(
            'Failure', 'Action creation failed.',
            '500 Internal Server Error');
    }

    $subject = sprintf(
        "Please activate your account at %s",
        get_setting(SITE_NAME));
    $body = fill_mail_template('activate_mail', array(
        'firstname' => $firstname,
        'actionurl' => get_action_url($actioncode)));
    send_system_mail($email, $subject, $body);
}

/**
 * Send an email with a password reset link if there is an account with the given email address.
 */
function send_reset_password_link($email) {
    if (($userinfo = find_user_firstname_login_uid_by_email($email)) === NULL) {
        return;
    }
    $firstname = $userinfo['ufname'];
    $login = $userinfo['ulogin'];
    $cuid = $userinfo['uid'];

    $actioncode = create_action_entry($cuid, 'reset_password', $email);
    if ($actioncode === FALSE) {
        errorpage(
            'Failure', 'Action creation failed.',
            '500 Internal Server Error');
    }

    $subject = sprintf(
        "Reset your password for %s",
        get_setting(SITE_NAME));
    $body = fill_mail_template(
        'reset_password', array(
            'firstname' => $firstname, 'login' => $login,
            'actionurl' => get_action_url($actioncode)));
    send_system_mail($email, $subject, $body);
}

/**
 * Send an email to confirm the change of a user's email address.
 */
function send_change_email_link($email, $uid) {
    if (($userinfo = find_user_firstname_login_uid_email_by_uid($uid)) == NULL)
    {
        return;
    }
    $firstname = $userinfo['ufname'];
    $login = $userinfo['ulogin'];
    $oldemail = $userinfo['uemail'];
    $cuid = $userinfo['uid'];

    $actioncode = create_action_entry($cuid, 'change_email', $email);
    if ($actioncode === FALSE) {
        errorpage(
            'Failure', 'Action creation failed.',
            '500 Internal Server Error');
    }

    $subject = sprintf(
        "Change your email address for %s",
        get_setting(SITE_NAME));
    $body = fill_mail_template(
        'change_email', array(
            'firstname' => $firstname, 'login' => $login,
            'actionurl' => get_action_url($actioncode),
            'oldemail' => $oldemail, 'email' => $email));
    send_system_mail($email, $subject, $body);
}

/**
 * Send an email to the site administrator with a user's request to delete his
 * account.
 */
function send_user_deletion($user, $id) {
    $subject = sprintf(
        "User %s requested his deletion",
        $user);
    $body = fill_mail_template(
        'delete_user', array('user' => $user, 'id' => $id));
    send_system_mail(get_setting(SITE_ADMINMAIL), $subject, $body);
}

/**
 * Unified formatting for timezone information.
 */
function format_timezone($timezone) {
    if ($timezone === NULL) {
        return "";
    }
    return sprintf(" %s", $timezone);
}

/**
 * Register a new coffee for the given user at the given time.
 */
function register_coffee($uid, $coffeetime, $timezone) {
    if (($cafinfo = find_recent_caffeine($coffeetime, $uid, 0)) !== NULL) {
        flash(sprintf(
            'Error: Your last coffee was less than 5 minutes ago at %s%s. O_o',
            $cafinfo['cdate'], format_timezone($cafinfo['ctimezone'])),
            FLASH_WARNING);
        return;
    }
    create_caffeine($coffeetime, $uid, 0);
    flash(
        sprintf(
           'Your coffee at %s%s has been registered!',
           $coffeetime, format_timezone($timezone)),
        FLASH_SUCCESS);
}

/**
 * Register a new mate for the given user at the given time.
 */
function register_mate($uid, $matetime, $timezone) {
    if (($cafinfo = find_recent_caffeine($matetime, $uid, 1)) !== NULL) {
        flash(sprintf(
            "Error: Your last mate was less than 5 minutes ago at %s%s. O_o",
            $cafinfo['cdate'], format_timezone($cafinfo['ctimezone'])),
            FLASH_WARNING);
        return;
    }
    create_caffeine($matetime, $uid, 1);
    flash(
        sprintf(
            'Your mate at %s%s has been registered!',
            $matetime, format_timezone($timezone)),
        FLASH_SUCCESS);
}

/**
 * Return a name for the given numeric entry type.
 */
function get_entrytype($entrytype) {
    global $ENTRY_TYPES;
    if (array_key_exists($entrytype, $ENTRY_TYPES)) {
        return $ENTRY_TYPES[$entrytype];
    }
    return "unknown";
}

/**
 * Load the profile information of the given user.
 */
function load_user_profile($loginid) {
    if (($userinfo = find_user_by_uid($loginid)) === NULL) {
        return NULL;
    }
    return array(
        'login' => $userinfo['ulogin'],
        'firstname' => $userinfo['ufname'],
        'lastname' => $userinfo['uname'],
        'location' => $userinfo['ulocation'],
        'email' => $userinfo['uemail'],
        'timezone' => $userinfo['utimezone']);
}
?>
