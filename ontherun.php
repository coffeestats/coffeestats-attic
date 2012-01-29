<?php
include('auth/config.php');
$token=mysql_real_escape_string($_GET['t']);
$user=mysql_real_escape_string($_GET['u']);
$ses_sql=mysql_query("select uid, utoken, ulogin from cs_users where ulogin='$user' and utoken='$token'; ");
$row=mysql_fetch_assoc($ses_sql);
$token=$row['utoken'];
$user=$row['ulogin'];
$profileid=$row['uid'];
if( (!isset($token)) && (!isset($user)) ) {
    header("Location: auth/login.php");
}
include("preheader.php");
	if($_SERVER["REQUEST_METHOD"] == "POST") {
      include('auth/config.php');
      echo("<div class=\"white-box\">");
      $coffeestamp=date('Y-m-d H:i:s', time());
          $sql="SELECT cid, cdate
                FROM cs_coffees
                WHERE cdate > (NOW() - INTERVAL '15:00' MINUTE_SECOND)
                AND (NOW() + INTERVAL '45:00' MINUTE_SECOND) > (cdate + INTERVAL '45' MINUTE_SECOND)
                AND cuid = '".$profileid."' ;";
	      $result=mysql_query($sql);
          $count=mysql_num_rows($result);
          if($count==0) {
		      $sql="INSERT INTO cs_coffees VALUES ('','".$_SESSION['login_id']."', NOW() ); ";
		      $result=mysql_query($sql);
		      echo("<p>Your coffee at ".$coffeestamp." was been registered!</p>");
          } else {
		      echo("<p>Error: Your last coffee was at least not 15 minutes ago. O_o</p>");
          }
		}
      echo("</div>");
?>

		<div class="white-box">
			<h2>On the run?</h2>
                <center>
				<form action="" method="post">
					<input class="imadecoffee" type="submit" value="Yes! And i got a coffee" id="coffee_plus_one" /><br />
				</form>
                </center> 

		</div>
<?php
include('footer.php');
?>
