<center><form action="" method="post">
<?php
require_once('lib/recaptchalib.php');
include('auth/config.php');
// Get a key from https://www.google.com/recaptcha/admin/create
$publickey = "6LdnPswSAAAAAFSYLEH9f_b0JcPQ2G1VsOHDmJZY";
$privatekey = "6LdnPswSAAAAALLCLsZt2AFTnl5VAcNH5WUDZBvf";

# the response from reCAPTCHA
$resp = null;
# the error code from reCAPTCHA, if any
$error = null;

# was there a reCAPTCHA response?
if ($_POST["recaptcha_response_field"]) {
        $resp = recaptcha_check_answer ($privatekey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"]);

        if ($resp->is_valid) {
          
              $sql="SELECT uid FROM cs_users WHERE ulogin='".$_POST['Login']."'";
              $result=mysql_query($sql);
              $row=mysql_fetch_array($result);
              $count=mysql_num_rows($result);

              if ($count == 0) { 
                echo "<br/><b>You got it! Click <a href=\"index.php\">here</a></b><br/><br/>";
                echo "<i>Yes. We hate CAPTCHAs too.</i><br/><br/><br/>";
                $sql="INSERT INTO cs_users VALUES ('', '".$_POST['Login']."', '', '', '".md5(md5($_POST['Password']))."', NOW(), '".$_POST['Location']."'); ";
                $result = mysql_query($sql) OR die(mysql_error());
              } else {
                echo("Sorry. Username already taken");
              }
        } else {
                # set the error code so that we can display it
                $error = $resp->error;
        }
}
?>
<table>
<tr>
    <td> Login: </td>
    <td> Password: </td>
    <td> EMail: </td>
    <td> Location: </td>
</tr>
<tr>
    <td><input type="TEXT" name="Login" maxlength="20" size="20"></td>
    <td><input type="PASSWORD" name="Password" maxlength="20" size="20"></td>
    <td><input type="TEXT" name="Email" maxlength="20" size="20"></td>
    <td><input type="TEXT" name="Location" maxlength="20" size="20"></td>
</tr>
</table>
<br/><br/>
<?php
echo recaptcha_get_html($publickey, $error);
?>
    <br/>
    <input type="submit" value="Register!" />
</form>
</center>
<?php include('footer.php'); ?>
