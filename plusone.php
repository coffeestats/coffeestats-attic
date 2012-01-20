<?php
include('auth/lock.php');
include("header.php"); 
	if($_SERVER["REQUEST_METHOD"] == "POST") {
      echo("<div class=\"white-box\">");
      $coffeestamp=date('Y-m-d i:s', time());
		include('auth/config.php');
        if($_POST['timestamp']) {
          $coffeedate=date('Y-m-d', time()) . " " .mysql_real_escape_string($_POST['timestamp']);
          if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}\ [0-9]{2}:[0-9]{2}/', $coffeedate))
          {
		    $sql="INSERT INTO cs_coffees VALUES ('','".$_SESSION['login_id']."', '".$coffeedate."' ); ";
		    $result=mysql_query($sql);
		    echo("<p>Your coffee at ".$coffeedate." was been registered!</p>");
          } else {
            echo("Sorry. This looks not like a valid time"); 
          }
        } else {
          $coffeedate=mysql_real_escape_string($_POST['timestamp']);
          $sql="select cid, cdate from cs_coffees WHERE cdate > (NOW() - INTERVAL '15:00' MINUTE_SECOND) and cuid = '".$_SESSION['login_id']."' ;";
	      $result=mysql_query($sql);
          $count=mysql_num_rows($result);
          if($count==0) {
		      $sql="INSERT INTO cs_coffees VALUES ('','".$_SESSION['login_id']."', NOW() ); ";
		      $result=mysql_query($sql);
		      echo("<p>Your coffee at ".$coffeestamp." was been registered!</p>");
          } else {
		      echo("<p>Error: Your last coffee was at least not 15 ago. O_o</p>");
          }
		}
      echo("</div>");
    }
?>

		<div class="white-box">
			<h2>Ahhh, another one, huh?</h2>
				<p>We know, you can't get your fingers away from coffee. Update your consume by +1!</p>
				
				<form action="" method="post">
					<input class="imadecoffee" type="submit" value="Right! And It tastes awesome" id="coffee_plus_one" /><br />
				</form>
				
		</div>
        <div class="white-box">
          <h2>Did you forgot a coffee today?</h2>
            <form action="" method="post">
              <input type="text" name="timestamp" placeholder="<?php echo date('H:i', time()); ?>" id="login_field_username" /> 
			  <input class="imadecoffee" type="submit" value="was the time" id="coffee_plus_one" /><br />
            </form>
        </div>

<?php
	include('footer.php');
?>

