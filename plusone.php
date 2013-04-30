<?php
include('auth/lock.php');
include("header.php");
include('lib/antixss.php');
	if($_SERVER["REQUEST_METHOD"] == "POST") {
        echo("<div class=\"white-box\">");
        // include('auth/config.php'); # already included in auth/lock.php

        if (array_key_exists('timestamp', $_POST) && !empty($_POST['timestamp'])) {
          $coffeedate=mysql_real_escape_string($_POST['timestamp']);
          $coffeedate=AntiXSS::setFilter($coffeedate, "whitelist", "string");
          if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}\ [0-9]{2}:[0-9]{2}/', $coffeedate))
          {
		    $sql="INSERT INTO cs_coffees VALUES ('','".$_SESSION['login_id']."', '".$coffeedate."' ); ";
		    $result=mysql_query($sql);
		    echo("Your coffee at ".$coffeedate." was been registered!");
          } else {
            echo("Sorry. This looks not like a valid time");
          }
        } elseif(array_key_exists('matetimestamp', $_POST) && !empty($_POST['matetimestamp'])) {
          $matedate=mysql_real_escape_string($_POST['matetimestamp']);
          $matedate=AntiXSS::setFilter($matedate, "whitelist", "string");
          if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}\ [0-9]{2}:[0-9]{2}/', $matedate))
          {
		    $sql="INSERT INTO cs_mate VALUES ('','".$_SESSION['login_id']."', '".$matedate."' ); ";
		    $result=mysql_query($sql);
		    echo("Your coffee at ".$matedate." was been registered!");
          } else {
            echo("Sorry. This looks not like a valid time");
          }
        } else {

          if(array_key_exists('coffeetime', $_POST)) {
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
          }

          if(array_key_exists('matetime', $_POST)) {
            $matedate=mysql_real_escape_string($_POST['matetime']);
            $matedate=AntiXSS::setFilter($matedate, "whitelist", "string");
            $sql="SELECT mid, mdate
                FROM cs_mate
                WHERE mdate > (NOW() - INTERVAL '5:00' MINUTE_SECOND)
                AND (NOW() + INTERVAL '45:00' MINUTE_SECOND) > (mdate + INTERVAL '45' MINUTE_SECOND)
                AND cuid = '".$_SESSION['login_id']."' ;";
	        $result=mysql_query($sql);
            $count=mysql_num_rows($result);
            if($count==0) {
		      $sql="INSERT INTO cs_mate VALUES ('','".$_SESSION['login_id']."', '".$matedate."' ); ";
		      $result=mysql_query($sql);
		      echo("Your mate at ".$matedate." was been registered!");
            } else {
		      echo("Error: Your last mate was at least not 5 minutes ago. O_o");
            }
          }
      }
          echo("</div>");
    }
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

    function toggle(control){
        var elem = document.getElementById(control);
    
        if(elem.style.display == "none"){
            elem.style.display = "inline";
        }else{
            elem.style.display = "none";
        }
    }
    </script>

		<div class="white-box">
			<h2>Coffee?</h2>
				<form action="" method="post" id="coffeeform">
                    <a href="javascript:toggle('specdate')"><img src="./images/revert.png"></a>
					<input class="imadecoffee" type="submit" value="Coffee!" id="coffee_plus_one" onclick="AddPostDataCoffee();" />
                    <div id="specdate" style="display: none">
                        <input type="text" name="timestamp" placeholder="<?php echo date('Y-m-d H:i', time()); ?>" id="login_field_username" />
                    </div>
                    <input type='hidden' id='coffeetime' name='coffeetime' value='' />
				</form>
                </div>


		</div>
        <div class="white-box">
          <h2>Mate?</h2>
			<form action="" method="post" id="mateform">
                <a href="javascript:toggle('specdatem')"><img src="./images/revert.png"></a>
				<input class="imademate" type="submit" value="Mate!" id="coffee_plus_one" onclick="AddPostDataMate();" />
                    <div id="specdatem" style="display: none">
                        <input type="text" name="matetimestamp" placeholder="<?php echo date('Y-m-d H:i', time()); ?>" id="login_field_username" />
                    </div>
                <input type='hidden' id='matetime' name='matetime' value='' />
				</form>
        </div>


<?php
	include('footer.php');
?>

