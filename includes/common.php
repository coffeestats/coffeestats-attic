<?php
if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    header('Status: 301 Moved Permanently');
    header('Location: ../index');
    exit();
}

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

define('PIWIK_HTTPS_URL', 'COFFEESTATS_PIWIK_HTTPS_URL');
define('PIWIK_HTTP_URL', 'COFFEESTATS_PIWIK_HTTP_URL');
define('PIWIK_SITE_ID', 'COFFEESTATS_PIWIK_SITEID');

define('RECAPTCHA_PRIVATEKEY', 'COFFEESTATS_RECAPTCHA_PRIVATEKEY');
define('RECAPTCHA_PUBLICKEY', 'COFFEESTATS_RECAPTCHA_PUBLICKEY');

define('SITE_SECRET', 'COFFEESTATS_SITE_SECRET');
define('SITE_NAME', 'COFFEESTATS_SITE_NAME');

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
 * Handle a MySQL error, log to error log and show an error page to the user.
 */
function handle_mysql_error($sql=NULL) {
    global $dbconn;
    if ($dbconn->errno !== 0) {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        error_log(sprintf(
            "%s line %d: MySQL error %d: %s%s",
            $backtrace[0]['file'], $backtrace[0]['line'],
            $dbconn->errno, $dbconn->error, ($sql === NULL) ? "" : "\n" . $sql));
        errorpage("Error", "Sorry, we have a problem.", "500 Internal Server Error");
    }
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
    return sprintf("%s://%s", $protocol, $_SERVER['SERVER_NAME']);
}

/**
 * Return a user's public profile URL.
 */
function public_url($username) {
    return sprintf("%s/profile?u=%s", baseurl(), urlencode($username));
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
 * Send a system mail to a given mail address.
 */
function send_system_mail($to, $subject, $body) {
    $from = sprintf('From: %s', get_setting(MAIL_FROM_ADDRESS));
    mail($to, $subject, $body, $from);
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
);

function create_action_entry($cuid, $action_type, $data) {
    global $dbconn, $ACTION_TYPES;
    $actioncode = generate_actioncode($data);
    $sql = sprintf(
        "INSERT INTO cs_actions
         (cuid, acode,
          created, validuntil,
          atype, adata)
         VALUES
         (%d, '%s',
          CURRENT_TIMESTAMP, CURRENT_TIMESTAMP + INTERVAL 24 HOUR,
          %d, '%s')",
        $cuid, $dbconn->real_escape_string($actioncode),
        $ACTION_TYPES[$action_type], $dbconn->real_escape_string($data));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    return $actioncode;
}

function get_action_url($actioncode) {
    return sprintf('%s/action?code=%s', baseurl(), urlencode($actioncode));
}

/**
 * Sends a mail to activate an account.
 */
function send_mail_activation_link($email) {
    global $dbconn;
    $sql = sprintf(
        "SELECT ufname, uid FROM cs_users WHERE uemail='%s'",
        $dbconn->real_escape_string($email));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $firstname = $row['ufname'];
        $cuid = $row['uid'];
    }
    else {
        errorpage(
            'Invalid data', 'Invalid data was sent',
            '500 Internal Server Error');
    }
    $result->close();

    $actioncode = create_action_entry($cuid, 'activate_mail', $email);

    $subject = sprintf(
        "Please activate your account at %s",
        get_setting(SITE_NAME));
    $body = str_replace(
        array('@firstname@', '@actionurl@'),
        array($firstname, get_action_url($actioncode)),
        file_get_contents(
            sprintf('%s/../templates/activate_mail.txt', dirname(__FILE__))));
    send_system_mail($email, $subject, $body);
}

/**
 * Send an email with a password reset link if there is an account with the given email address.
 */
function send_reset_password_link($email) {
    global $dbconn;
    $sql = sprintf(
        "SELECT ufname, ulogin, uid FROM cs_users WHERE uemail='%s'",
        $dbconn->real_escape_string($email));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $firstname = $row['ufname'];
        $login = $row['ulogin'];
        $cuid = $row['uid'];
    }
    $result->close();
    if (!isset($cuid)) {
        return;
    }

    $actioncode = create_action_entry($cuid, 'reset_password', $email);

    $subject = sprintf(
        "Reset your password for %s",
        get_setting(SITE_NAME));
    $body = str_replace(
        array('@firstname@', '@login@', '@actionurl@'),
        array($firstname, $login, get_action_url($actioncode)),
        file_get_contents(
            sprintf('%s/../templates/reset_password.txt', dirname(__FILE__))));
    send_system_mail($email, $subject, $body);
}

/**
 * Performs a cleanup of the action table.
 */
function clean_expired_actions() {
    global $dbconn;
    $sql = "DELETE FROM cs_actions WHERE validuntil < CURRENT_TIMESTAMP";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
}

/**
 * Perform a cleanup of inactive users that have no coffee or mate registered
 * yet.
 */
function clean_inactive_users() {
    global $dbconn;
    $sql = "DELETE FROM cs_users
        WHERE uactive=0 AND NOT EXISTS (
          SELECT cid FROM cs_caffeine WHERE cuid=uid)
        AND ujoined < (CURRENT_TIMESTAMP - INTERVAL 30 DAY)";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
}

/**
 * Register a new coffee for the given user at the given time.
 */
function register_coffee($uid, $coffeetime) {
    global $dbconn;
    $sql = sprintf(
        'SELECT cid, cdate
         FROM cs_caffeine
         WHERE ctype = 0
           AND cdate > (\'%1$s\' - INTERVAL 5 MINUTE)
           AND cdate < (\'%1$s\' + INTERVAL 5 MINUTE)
           AND cuid = %2$d',
        $dbconn->real_escape_string($coffeetime), $uid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $result->close();
        flash(sprintf(
            'Error: Your last coffee was less than 5 minutes ago at %s. O_o',
            $row['cdate']),
            FLASH_WARNING);
    }
    else {
        $result->close();
        $sql = sprintf(
            "INSERT INTO cs_caffeine (cuid, ctype, cdate, centrytime)
             VALUES (%d, 0, '%s', UTC_TIMESTAMP)",
            $uid, $dbconn->real_escape_string($coffeetime));
        if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
            handle_mysql_error();
        }
        flash(sprintf(
            'Your coffee at %s has been registered!', $coffeetime),
            FLASH_SUCCESS);
    }
}

/**
 * Register a new mate for the given user at the given time.
 */
function register_mate($uid, $matetime) {
    global $dbconn;
    $sql = sprintf(
        'SELECT cid, cdate
         FROM cs_caffeine
         WHERE ctype = 1
           AND cdate > (\'%1$s\' - INTERVAL 5 MINUTE)
           AND cdate < (\'%1$s\' + INTERVAL 5 MINUTE)
           AND cuid = %2$d',
        $dbconn->real_escape_string($matetime), $uid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $result->close();
        flash(sprintf(
            "Error: Your last mate was less than 5 minutes ago at %s. O_o",
            $row['cdate']),
            FLASH_WARNING);
    }
    else {
        $result->close();
        $sql=sprintf(
            "INSERT INTO cs_caffeine (cuid, ctype, cdate, centrytime)
             VALUES (%d, 1, '%s', UTC_TIMESTAMP)",
            $uid, $dbconn->real_escape_string($matetime));
        if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
            handle_mysql_error();
        }
        flash(sprintf(
            'Your mate at %s has been registered!', $matetime),
            FLASH_SUCCESS);
    }
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
?>
