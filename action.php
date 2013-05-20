<?php
include('auth/config.php');
include_once('includes/queries.php');

if (!isset($_GET['code']) || empty($_GET['code'])) {
    errorpage(
        'Bad request',
        'Your request could not be processed',
        '400 Bad Request');
}

$actiondata = find_action_data($_GET['code']);
if ($actiondata === NULL) {
    errorpage(
        'Invalid action code',
        'The action code in the URL you tried to access is not valid. ' .
        'Maybe you entered a wrong code or the code has expired.',
        '404 Not Found');
}

$cuid = $actiondata['cuid'];
$atype = $actiondata['atype'];
$adata = $actiondata['adata'];

function activate_account($cuid) {
    if (set_user_active($cuid)) {
        flash("Your account has been activated successfully.", FLASH_SUCCESS);
    }
}

function reset_password($cuid) {
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['reset_password_uid'] = $cuid;
    redirect_to('auth/changepassword');
}

function change_email($cuid, $email) {
    if (set_user_email($cuid, $email)) {
        flash("Your email address has been changed successfully.", FLASH_SUCCESS);
    }
}

switch ($atype) {
case $ACTION_TYPES['activate_mail']:
    delete_action($_GET['code']);
    activate_account($cuid);
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
