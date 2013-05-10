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
function handle_mysql_error() {
    global $dbconn;
    if ($dbconn->errno !== 0) {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        error_log(sprintf(
            "%s line %d: MySQL error %d: %s",
            $backtrace[0]['file'], $backtrace[0]['line'],
            $dbconn->errno, $dbconn->error));
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
);

/**
 * Sends a mail to activate an account.
 */
function send_mail_activation_link($email) {
    global $dbconn, $ACTION_TYPES;
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

    $actioncode = generate_actioncode($email);
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
        $ACTION_TYPES['activate_mail'], $dbconn->real_escape_string($email));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }

    $subject = sprintf(
        "Please activate your account at %s",
        get_setting(SITE_NAME));
    $body = str_replace(
        array('@firstname@', '@actionurl@'),
        array($firstname, sprintf('%s/action?code=%s', baseurl(), urlencode($actioncode))),
        file_get_contents(
            sprintf('%s/../templates/activate_mail.txt', dirname(__FILE__))));
    send_system_mail($email, $subject, $body);
}
?>
