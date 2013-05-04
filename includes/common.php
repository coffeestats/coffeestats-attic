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
?>
