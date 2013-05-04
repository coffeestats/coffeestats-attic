<?php
include_once(sprintf('%s/../includes/common.php', dirname(__FILE__)));

if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    redirect_to('../index', TRUE);
}

include_once('config.php');
if (!isset($_SESSION)) {
    session_start();
}
if (isset($_SESSION['login_user'])) {
    $user_check=$_SESSION['login_user'];
    $ses_sql=mysql_query(
        sprintf(
            "SELECT ulogin FROM cs_users WHERE ulogin='%s'",
            $user_check));
    $row=mysql_fetch_assoc($ses_sql);
    $login_session=$row['ulogin'];
}

if (!isset($login_session)) {
    redirect_to('auth/login');
}
?>
