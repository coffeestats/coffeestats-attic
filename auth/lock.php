<?php
include('config.php');
session_start();
if (array_key_exists('login_user', $_SESSION)) {
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
