<?php
include('config.php');
session_start();
$user_check=$_SESSION['login_user'];
echo $_SESSION['login_user'];
$ses_sql=mysql_query("select ulogin from cs_users where ulogin='$user_check' ");
$row=mysql_fetch_array($ses_sql);
$login_session=$row['ulogin'];
if(!isset($login_session))
{
  header("Location: auth/login.php");
}
?>
