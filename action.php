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

switch ($atype) {
case $ACTION_TYPES['activate_mail']:
    activate_account($cuid);
    break;
default:
    errorpage(
        'Bad request',
        'Your request could not be processed',
        '400 Bad Request');
}

$sql = sprintf(
    "DELETE FROM cs_actions WHERE acode='%s'",
    $dbconn->real_escape_string($_GET['code']));
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}

redirect_to('index');
?>
