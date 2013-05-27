<?php
include_once(sprintf('%s/../includes/common.php', dirname(__FILE__)));

if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    redirect_to('../index', TRUE);
}

include_once(sprintf('%s/../includes/queries.php', dirname(__FILE__)));

if (!isset($_SESSION)) {
    session_start();
}
if (isset($_SESSION['login_user'])) {
    $user_check = $_SESSION['login_user'];
    $login_session = get_login_for_user_with_login($_SESSION['login_user']);
}

if (!isset($login_session) || ($login_session === NULL)) {
    redirect_to('/auth/login');
}
?>
