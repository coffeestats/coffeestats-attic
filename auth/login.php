<?php
include("config.php");
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST")
{
// username and password sent from Form
$myusername=addslashes($_POST['username']);
$mypassword=md5(md5(addslashes($_POST['password'])));

$sql="SELECT uid FROM cs_users WHERE ulogin='$myusername' and ucryptsum='$mypassword'";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$count=mysql_num_rows($result);
$id=$row['uid'];
// If result matched $myusername and $mypassword, table row must be 1 row

  if($count==1)
  {
    session_register("myusername");
    $_SESSION['login_user']=$myusername;
    $_SESSION['login_id']=$id;
    header("location: ../index.php");
  }
  else
  {
    $error="Your Login Name or Password is invalid";
    echo("$error");
  }
}
include("../preheader.php");
?>
<form action="" method="post">
<center>
<table><tr>
    <td> Login: </td>
    <td> Password: </td>
</tr>
<tr>
    <td><input type="text" name="username"/><br /></td>
    <td><input type="password" name="password"/><br/></td>
</tr>
</table>
    <?php if (isset($error)) { echo("<br/>$error<br/>"); } ; ?> 
    <br/><input type="submit" value=" Login! "/> or simply <a href="register.php">register a new account</a>!
</center>
</form>

