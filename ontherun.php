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
include('auth/config.php');
include('lib/antixss.php');
	if($_POST['coffeetime']) {
      echo("<div class=\"white-box\">");
          $coffeedate=mysql_real_escape_string($_POST['coffeetime']);
          $coffeedate=AntiXSS::setFilter($coffeedate, "whitelist", "string");
          $sql="SELECT cid, cdate
                FROM cs_coffees
                WHERE cdate > (NOW() - INTERVAL '5:00' MINUTE_SECOND)
                AND (NOW() + INTERVAL '45:00' MINUTE_SECOND) > (cdate + INTERVAL '45' MINUTE_SECOND)
                AND cuid = '".$profileid."' ;";
	      $result=mysql_query($sql);
          $count=mysql_num_rows($result);
          if($count==0) {
		      $sql="INSERT INTO cs_coffees VALUES ('','".$profileid."', '".$coffeedate."' ); ";
		      $result=mysql_query($sql);
		      echo("Your coffee at ".$coffeedate." was been registered!");
          } else {
		      echo("Error: Your last coffee was at least not 5 minutes ago. O_o");
          } 
    } elseif($_POST['matetime']) {  
      echo("<div class=\"white-box\">");
          $matedate=mysql_real_escape_string($_POST['matetime']);
          $matedate=AntiXSS::setFilter($matedate, "whitelist", "string");
          $sql="SELECT mid, mdate
                FROM cs_mate
                WHERE mdate > (NOW() - INTERVAL '5:00' MINUTE_SECOND)
                AND (NOW() + INTERVAL '45:00' MINUTE_SECOND) > (mdate + INTERVAL '45' MINUTE_SECOND)
                AND cuid = '".$profileid."' ;";
          $result=mysql_query($sql);
          $count=mysql_num_rows($result);
          if($count==0) {
              $sql="INSERT INTO cs_mate VALUES ('','".$profileid."', '".$matedate."' ); ";
              $result=mysql_query($sql);
              echo("Your mate at ".$matedate." was been registered!");
          } else {
              echo("Error: Your last mate was at least not 5 minutes ago. O_o");
          } 
    }
      echo("</div>");
?>

    <script type="text/javascript">
    function AddPostDataCoffee() {
      function coffeetime(d){
        function pad(n){return n<10 ? '0'+n : n}
        return d.getFullYear()+'-'
        + pad(d.getMonth()+1)+'-'
        + pad(d.getDate())+' '
        + pad(d.getHours())+':'
        + pad(d.getMinutes())+':'
        + pad(d.getSeconds())
      }
      var d = new Date();
      document.getElementById('coffeetime').value = coffeetime(d);
      document.getElementById("coffeeform").submit();
    }
    function AddPostDataMate() {
      function coffeetime(d){
        function pad(n){return n<10 ? '0'+n : n}
        return d.getFullYear()+'-'
        + pad(d.getMonth()+1)+'-'
        + pad(d.getDate())+' '
        + pad(d.getHours())+':'
        + pad(d.getMinutes())+':'
        + pad(d.getSeconds())
      }
      var d = new Date();
      document.getElementById('matetime').value = coffeetime(d);
      document.getElementById("mateform").submit();
    }
    </script>


		<div class="white-box">
			<h2>On the run?</h2>
                <center>
				<form action="" method="post" id="coffeeform" >
					<input class="imadecoffee" type="submit" value="Coffee!" id="coffee_plus_one" onclick="AddPostDataCoffee();" /><br />
                    <input type='hidden' id='coffeetime' name='coffeetime' value='' />
				</form>
				<form action="" method="post" id="mateform" >
					<input class="imademate" type="submit" value="Mate!" id="coffee_plus_one" onclick="AddPostDataMate();" /><br />
                    <input type='hidden' id='matetime' name='matetime' value='' />
				</form>
                </center> 

		</div>
<?php
include('footer.php');
?>
