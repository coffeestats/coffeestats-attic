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
    $sql = sprintf(
        "SELECT ulogin FROM cs_users WHERE ulogin='%s'",
        $user_check);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    elseif ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $login_session = $row['ulogin'];
    }
    $result->close();
}

if (!isset($login_session)) {
    redirect_to('auth/login');
}
?>
