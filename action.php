<?php
include('auth/config.php');

if (!isset($_GET['code']) || empty($_GET['code'])) {
    errorpage(
        'Bad request',
        'Your request could not be processed',
        '400 Bad Request');
}

$sql = sprintf(
    "SELECT cuid, atype, adata FROM cs_actions WHERE acode='%s'",
    $dbconn->real_escape_string($_GET['code']));
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $cuid = $row['cuid'];
    $atype = $row['atype'];
    $adata = $row['adata'];
}
else {
    errorpage(
        'Invalid action code',
        'The action code in the URL you tried to access is not valid. ' .
        'Maybe you entered a wrong code or the code has expired.',
        '404 Not Found');
}
$result->close();

function delete_action($actioncode) {
    global $dbconn;
    $sql = sprintf(
        "DELETE FROM cs_actions WHERE acode='%s'",
        $dbconn->real_escape_string($actioncode));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
}

function activate_account($cuid) {
    global $dbconn;
    $sql = sprintf(
        "UPDATE cs_users SET uactive=1 WHERE uid=%d",
        $cuid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    flash("Your account has been activated successfully.", FLASH_SUCCESS);
}

function reset_password($cuid) {
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['reset_password_uid'] = $cuid;
    redirect_to('auth/changepassword');
}

function change_email($cuid, $email) {
    global $dbconn;
    $sql = sprintf(
        "UPDATE cs_users SET uemail='%s' WHERE uid=%d",
        $dbconn->real_escape_string($email), $cuid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    flash("Your email address has been changed successfully.", FLASH_SUCCESS);
}

switch ($atype) {
case $ACTION_TYPES['activate_mail']:
    activate_account($cuid);
    delete_action($_GET['code']);
    redirect_to('index');
    break;
case $ACTION_TYPES['reset_password']:
    delete_action($_GET['code']);
    reset_password($cuid);
    break;
case $ACTION_TYPES['change_email']:
    delete_action($_GET['code']);
    change_email($cuid, $adata);
    redirect_to('index');
    break;
default:
    errorpage(
        'Bad request',
        'Your request could not be processed',
        '400 Bad Request');
}
?>
