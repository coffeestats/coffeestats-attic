<?php
include('auth/lock.php');
include("header.php");
include('lib/antixss.php');
	if($_SERVER["REQUEST_METHOD"] == "POST") {
      echo("<div class=\"white-box\">");
		include('auth/config.php');
        if($_POST['timestamp']) {
          $coffeedate=mysql_real_escape_string($_POST['timestamp']);
          $coffeedate=AntiXSS::setFilter($coffeedate, "whitelist", "string");
          if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}\ [0-9]{2}:[0-9]{2}/', $coffeedate))
          {
		    $sql="INSERT INTO cs_coffees VALUES ('','".$_SESSION['login_id']."', '".$coffeedate."' ); ";
		    $result=mysql_query($sql);
		    echo("<p>Your coffee at ".$coffeedate." was been registered!</p>");
          } else {
            echo("Sorry. This looks not like a valid time");
          }
        } else {
          if($_POST['coffeetime']) {
            $coffeedate=mysql_real_escape_string($_POST['coffeetime']);
            $coffeedate=AntiXSS::setFilter($coffeedate, "whitelist", "string");
            $sql="SELECT cid, cdate
                FROM cs_coffees
                WHERE cdate > (NOW() - INTERVAL '5:00' MINUTE_SECOND)
                AND (NOW() + INTERVAL '45:00' MINUTE_SECOND) > (cdate + INTERVAL '45' MINUTE_SECOND)
                AND cuid = '".$_SESSION['login_id']."' ;";
	        $result=mysql_query($sql);
            $count=mysql_num_rows($result);
            if($count==0) {
		      $sql="INSERT INTO cs_coffees VALUES ('','".$_SESSION['login_id']."', '".$coffeedate."' ); ";
		      $result=mysql_query($sql);
		      echo("Your coffee at ".$coffeedate." was been registered!");
            } else {
		      echo("Error: Your last coffee was at least not 5 minutes ago. O_o");
            }
          } else
		      echo("Error: Are you sure you inserted the data we need?");
		}
      echo("</div>");
    }
?>

    <script type="text/javascript">
    function coffeetime(d){
      function pad(n){return n<10 ? '0'+n : n}
      return d.getFullYear()+'-'
        + pad(d.getMonth()+1)+'-'
        + pad(d.getDate())+' '
        + pad(d.getHours())+':'
        + pad(d.getMinutes())+':'
        + pad(d.getSeconds())}
        
    var d = new Date();

    function AddPostData() {
      document.getElementById('coffeetime').value = coffeetime(d);
      document.getElementById("coffeeform").submit();
    }
    </script>

		<div class="white-box">
			<h2>Ahhh, another one, huh?</h2>
				<p>We know, we can't control ourselves either; Update your consumption by +1!</p>

				<form action="" method="post" id="coffeeform">
					<input class="imadecoffee" type="submit" value="Right! And It tastes awesome" id="coffee_plus_one" onclick="AddPostData();" /><br />
                    <input type='hidden' id='coffeetime' name='coffeetime' value='' />
				</form>

		</div>
        <div class="white-box">
          <h2>(Secretly) chugged down a cup of coffee and forgot to tell us about it?</h2>
            <form action="" method="post" id="coffeewasform">
              <input type="text" name="timestamp" placeholder="<?php echo date('Y-m-d H:i', time()); ?>" id="login_field_username" />
			  <input class="imadecoffee" type="submit" value="was the time" id="coffee_plus_one" /><br />
            </form>
        </div>

<?php
	include('footer.php');
?>

