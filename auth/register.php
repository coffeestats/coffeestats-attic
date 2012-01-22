<form action="" method="post">
<?php
	include('config.php');
	include('../preheader.php');
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
              if (!isset($_POST['Login'])) {
                $cerr=2;
              } 
              if (!ctype_alnum($_POST['Login'])) {
                $cerr=2;
              } 
              if (!isset($_POST['Email'])) {
                $cerr=2;
              }
              if (!isset($_POST['Password'])) {
                $cerr=2;
              }
            $login=mysql_real_escape_string($_POST['Login']);
              $email=mysql_real_escape_string($_POST['Email']);
              $forename=mysql_real_escape_string($_POST['Forename']);
              $name=mysql_real_escape_string($_POST['Name']);
              $password=crypt(mysql_real_escape_string($_POST['Password']), '$2a$07$thisissomefuckingassholesaltforcoffeestats$');
              $location=mysql_real_escape_string($_POST['Location']);
              $sql="SELECT uid FROM cs_users WHERE ulogin='".$login."'; ";
              $result=mysql_query($sql);
              $row=mysql_fetch_array($result);
              $count=mysql_num_rows($result);
               if (($count == 0) && ($cerr == 0)) { 
                echo "<div class=\"white-box\"><h2>You got it! Click <a href=\"../index\">here</a></h2>";
                echo "Yes. We hate CAPTCHAs too.</div>";
                $sql="INSERT INTO cs_users VALUES ('', '".$login."', '".$email."', '".$forename."', '".$name."', '".$password."', NOW(), '".$location."', 'yes'); ";
                $result = mysql_query($sql); 
              } 
              
              else {
                echo("<div class=\"white-box\">Error: Sorry. Username already taken, invalid or you forgot something in General section.</div>");
              }
        } 
        
        else {
        	# set the error code so that we can display it
            $error = $resp->error;
        }
    }
?>
        <div class="white-box">
            <h2>Register</h2>
			<p>Fill these fields with your data, write down what reCAPTCHA says u and click Register!</p>
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
        
        <div class="white-box">
		    <p><a href="#">coffeestats.org</a> is a project by <a href="http://noqqe.de">Florian Baumann</a> & <a href="http://savier.n0q.org">Holger Winter</a>. If you like you can follow us on <a href="#">Facebook</a>. We wouldn't be sad if you click "i like", either!</p>
		</div>
</body>
</html>
