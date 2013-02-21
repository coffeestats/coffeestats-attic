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
	if($_POST['coffeetime']) {
      include('auth/config.php');
      include('lib/antixss.php');
      echo("<div class=\"white-box\">");
          $coffeedate=mysql_real_escape_string($_POST['coffeetime']);
          $coffeedate=AntiXSS::setFilter($coffeedate, "whitelist", "string");
          $sql="SELECT cid, cdate
                FROM cs_coffees
                WHERE cdate > (NOW() - INTERVAL '1:00' MINUTE_SECOND)
                AND (NOW() + INTERVAL '45:00' MINUTE_SECOND) > (cdate + INTERVAL '45' MINUTE_SECOND)
                AND cuid = '".$profileid."' ;";
	      $result=mysql_query($sql);
          $count=mysql_num_rows($result);
          if($count==0) {
		      $sql="INSERT INTO cs_coffees VALUES ('','".$profileid."', '".$coffeedate."' ); ";
		      $result=mysql_query($sql);
		      echo("Your coffee at ".$coffeetime." was been registered!");
          } else {
		      echo("Error: Your last coffee was at least not 5 minutes ago. O_o");
          } 
        } 
      echo("</div>");
?>

    <script type="text/javascript">
    function AddPostData() {
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
    </script>


		<div class="white-box">
			<h2>On the run?</h2>
                <center>
				<form action="" method="post" id="coffeeform" >
					<input class="imadecoffee" type="submit" value="Yes! And i got a coffee" id="coffee_plus_one" onclick="AddPostData();" /><br />
                    <input type='hidden' id='coffeetime' name='coffeetime' value='' />
				</form>
                </center> 

		</div>
<?php
include('footer.php');
?>
