<?php
include('auth/lock.php');
include('lib/antixss.php');
include_once('includes/common.php');

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['timestamp']) && !empty($_POST['timestamp'])) {
        // TODO: implement proper input validation function (see https://bugs.n0q.org/view.php?id=13)
        $coffeedate = AntiXSS::setFilter($_POST['timestamp'], "whitelist", "string");
        if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}\ [0-9]{2}:[0-9]{2}/', $coffeedate)) {
            $sql = sprintf(
                "INSERT INTO cs_coffees (cuid, cdate) VALUES (%d, '%s')",
                $_SESSION['login_id'],
                $dbconn->real_escape_string($coffeedate));
            if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
                handle_mysql_error();
            }
            flash(sprintf(
                "Your coffee at %s has been registered!", $coffeedate),
                FLASH_SUCCESS);
        }
        else {
            flash("Sorry. This looks not like a valid time", FLASH_ERROR);
        }
    }
    elseif (isset($_POST['matetimestamp']) && !empty($_POST['matetimestamp'])) {
        // TODO: implement proper input validation function (see https://bugs.n0q.org/view.php?id=13)
        $matedate=AntiXSS::setFilter($_POST['matetimestamp'], "whitelist", "string");
        if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}\ [0-9]{2}:[0-9]{2}/', $matedate)) {
            $sql=sprintf(
                "INSERT INTO cs_mate (cuid, cdate) VALUES (%d, '%s')",
                $_SESSION['login_id'],
                $dbconn->real_escape_string($matedate));
            if (($result=$dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
                handle_mysql_error();
            }
            flash(sprintf(
                "Your coffee at %s has been registered!", $matedate),
                FLASH_SUCCESS);
        }
        else {
            flash("Sorry. This looks not like a valid time", FLASH_ERROR);
        }
    }
    elseif (isset($_POST['coffeetime'])) {
        $coffeedate=AntiXSS::setFilter($_POST['coffeetime'], "whitelist", "string");
        $sql=sprintf(
            "SELECT cid, cdate
             FROM cs_coffees
             WHERE cdate > (NOW() - INTERVAL '5:00' MINUTE_SECOND)
             AND (NOW() + INTERVAL '45:00' MINUTE_SECOND) > (cdate + INTERVAL '45' MINUTE_SECOND)
             AND cuid = %d",
            $_SESSION['login_id']);
        if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
            handle_mysql_error();
        }
        if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $result->close();
            flash(sprintf(
                "Error: Your last coffee was less than 5 minutes ago at %s. O_o",
                $row['cdate']),
                FLASH_WARNING);
        }
        else {
            $result->close();
            $sql = sprintf(
                "INSERT INTO cs_coffees (cuid, cdate) VALUES (%d,'%s')",
                $_SESSION['login_id'],
                $dbconn->real_escape_string($coffeedate));
            if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
                handle_mysql_error();
            }
            flash(sprintf(
                "Your coffee at %s has been registered!",
                $coffeedate),
                FLASH_SUCCESS);
        }
    }
    elseif (isset($_POST['matetime'])) {
        $matedate=AntiXSS::setFilter($_POST['matetime'], "whitelist", "string");
        $sql=sprintf(
            "SELECT mid, mdate
             FROM cs_mate
             WHERE mdate > (NOW() - INTERVAL '5:00' MINUTE_SECOND)
             AND (NOW() + INTERVAL '45:00' MINUTE_SECOND) > (mdate + INTERVAL '45' MINUTE_SECOND)
             AND cuid = %d",
            $_SESSION['login_id']);
        if (($result=$dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
            handle_mysql_error();
        }
        if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $result->close();
            flash(sprintf(
                "Error: Your last mate was less than 5 minutes ago at %s. O_o",
                $row['mdate']),
                FLASH_WARNING);
        }
        else {
            $result->close();
            $sql = sprintf(
                "INSERT INTO cs_mate (cuid, mdate) VALUES (%d, '%s')",
                $_SESSION['login_id'],
                $dbconn->real_escape_string($matedate));
            if (($result=$dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
                handle_mysql_error();
            }
            flash(sprintf(
                "Your mate at %s was been registered!",
                $matedate),
                FLASH_SUCCESS);
        }
    }
}
include("header.php");
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
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="coffeeform">
        <div>
            <a href="javascript:toggle('specdate')"><img src="./images/revert.png"></a>
            <input class="imadecoffee" type="submit" value="Coffee!" id="coffee_plus_one" onclick="AddPostDataCoffee();" />
            <div id="specdate" style="display: none">
                <input type="text" name="timestamp" placeholder="<?php echo date('Y-m-d H:i', time()); ?>" class="default_input_field datetime_field" />
            </div>
            <input type='hidden' id='coffeetime' name='coffeetime' value='' />
        </div>
    </form>
</div>

<div class="white-box">
    <h2>Mate?</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="mateform">
        <div>
            <a href="javascript:toggle('specdatem')"><img src="./images/revert.png"></a>
            <input class="imademate" type="submit" value="Mate!" id="coffee_plus_one" onclick="AddPostDataMate();" />
            <div id="specdatem" style="display: none">
                <input type="text" name="matetimestamp" placeholder="<?php echo date('Y-m-d H:i', time()); ?>" class="default_input_field datetime_field" />
            </div>
            <input type='hidden' id='matetime' name='matetime' value='' />
        </div>
    </form>
</div>
<?php
include('footer.php');
?>
