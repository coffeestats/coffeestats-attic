<?php
include_once('includes/common.php');
if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    redirect_to('index', TRUE);
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="author" content="F. Baumann, H. Winter, J. Dittberner" />
    <meta name="description" content="coffeestats.org | All about coffee" />
    <title>coffeestats.org</title>
    <link rel="stylesheet" type="text/css" href="../css/caffeine.css" />
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1><a href="/">coffeestats.org</a></h1>
            <p>...about what keeps you awake at night.</p>
        </div>
        <div id="content">
<?php
if (isset($login_session)) {
?>
            <div class="white-box">
                <div id="navigation">
                    <ul>
                        <li id="navindex"><a href="index">Home</a></li>
                        <li id="navplusone"><a href="plusone">Update</a></li>
                        <li id="navprofile"><a href="profile">Profile</a></li>
                        <li id="navexplore"><a href="explore">Explore</a></li>
                        <li id="navoverall"><a href="overall">Overall Stats</a></li>
                        <li id="navabout"><a href="about">About</a></li>
                        <li id="navlogout"><a href="auth/logout">Logout</a></li>
                    </ul>
                </div>
            </div>
<?php
}

if (peek_flash()) {
?>
            <div class="white-box">
<?php
    while (($message = pop_flash()) !== NULL) {
?>
                <p class="flash-<?php echo $message[0]; ?>"><?php echo $message[1]; ?></p>
<?php
    }
?>
            </div>
<?php
}
?>
