<?php
if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    header('Status: 301 Moved Permanently');
    header('Location: ../index');
    exit();
}

include('config.php');
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

if(!isset($login_session)) {
    header("Location: auth/login");
    exit(); // stop execution if not logged in
}
?>
