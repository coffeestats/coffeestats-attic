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
    <link rel="shortcut icon" href="../images/favicon.png" type="image/x-icon">
</head>
<body>
    <div id="wrapper">
<ul class="flash-messages" id="system-flash"><?php
if (peek_flash()) {
    while (($message = pop_flash()) !== NULL) {
?><li class="flash-<?php echo $message[0]; ?>"><?php echo $message[1]; ?> <a href="#" class="close">X</a></li><?php
    }
}
?></ul>
      <div id="header">
<?php
if (isset($login_session)) {
?>
          <div id="account" class="rightfloated"><a href="settings">Settings</a> / <a href="auth/logout">Logout</a></div>
<?php
}
?>
          <h1><a href="/">coffeestats.org</a></h1>
          <p>...about what keeps you awake at night.</p>
      </div>
      <div id="content">
<?php
if (isset($login_session)) {
?>
          <div id="navigation">
              <ul>
                  <li><a href="index" class="navindex">Home</a></li>
                  <li><a href="profile" class="navprofile">Profile</a></li>
                  <li><a href="explore" class="navexplore">Explore</a></li>
                  <li><a href="overall" class="navoverall">Overall Stats</a></li>
                  <li><a href="about" class="navabout">About</a></li>
              </ul>
          </div>
<?php
}
?>
