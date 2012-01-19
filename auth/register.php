<?php
	include('config.php');
	include('../preheader.php');
?>

		<div class="white-box">
            <h2>Register</h2>
			<p>Fill these fields with your data, write down what reCAPTCHA says u and click Register!</p>
			
			<form action="" method="post">

<?php
	require_once('../lib/recaptchalib.php');

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
              $login=mysql_real_escape_string($_POST['Login']);
              $email=mysql_real_escape_string($_POST['Email']);
              $forename=mysql_real_escape_string($_POST['Forename']);
              $name=mysql_real_escape_string($_POST['Name']);
              $password=mysql_real_escape_string(md5(md5($_POST['Password'])));
              $location=mysql_real_escape_string($_POST['Location']);

              echo("$login $email $forename $name $password $location");
              $sql="SELECT uid FROM cs_users WHERE ulogin='".$login."'";
              $result=mysql_query($sql);
              $row=mysql_fetch_array($result);
              $count=mysql_num_rows($result);


              if ($count == 0) { 
                echo "<br/><b>You got it! Click <a href=\"../index.php\">here</a></b><br/>";
                echo "<i>Yes. We hate CAPTCHAs too.</i><br/><br/><br/>";
                $sql="INSERT INTO cs_users VALUES ('', '".$login."', '".$email."', '$forename', '$name', '".$password."', NOW(), '".$location."'); ";
                $result = mysql_query($sql) OR die(mysql_error());
              } 
              
              else {
                echo("Sorry. Username already taken");
              }
        } 
        
        else {
        	# set the error code so that we can display it
            $error = $resp->error;
        }
	}
?>
            <b>General</b><br/>
			<input type="text" name="Login" maxlength="20" placeholder="Username" id="register_field_standard" />
			<input type="password" name="Password" maxlength="20" placeholder="Password" id="register_field_standard" />
			<input type="text" name="Email" maxlength="30" placeholder="E-Mail" id="register_field_standard" />

            <br/><br/><b>Additional</b><br/>
			<input type="text" name="Forename" maxlength="20" placeholder="Forename" id="register_field_standard" />
			<input type="text" name="Name" maxlength="20" placeholder="Name" id="register_field_standard" />
			<input type="text" name="Location" maxlength="20" placeholder="Location" id="register_field_standard" />
		</div> <!-- end of white-box -->
		
		<div class="white-box">
			<?php
				echo recaptcha_get_html($publickey, $error);
			?>		
		</div>
		
		<div class="white-box">
			<input type="submit" value="Register!" id="register_button_standard" />
		</div>    
</form>

<?php 
	include('../footer.php'); 
?>
