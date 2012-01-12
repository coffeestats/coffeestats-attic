<?php
include("config.php");
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST")
{
// username and password sent from Form
$myusername=addslashes($_POST['username']);
$mypassword=md5(addslashes($_POST['password']));

$sql="SELECT uid FROM cs_users WHERE ulogin='$myusername' and ucryptsum='$mypassword'";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$count=mysql_num_rows($result);
// If result matched $myusername and $mypassword, table row must be 1 row

  if($count==1)
  {
    session_register("myusername");
    $_SESSION['login_user']=$myusername;
    header("location: ../index.php");
  }
  else
  {
    $error="Your Login Name or Password is invalid";
  }
}
?>

<form action="" method="post">
<label>UserName :</label>
<input type="text" name="username"/><br />
<label>Password :</label>
<input type="password" name="password"/><br/>
<input type="submit" value=" Login "/><br />
</form>

Or simply <a href="../register.php">register a new account</a>!
