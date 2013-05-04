<?php
include('auth/config.php');
include('lib/antixss.php');
include_once('includes/common.php');

if (isset($_GET['t']) && isset($_GET['u'])) {
    $token = AntiXSS::setFilter($_GET['t'], 'whitelist', 'string');
    $user = AntiXSS::setFilter($_GET['u'], 'whitelist', 'string');
    $sql = sprintf(
        "SELECT uid, utoken, ulogin FROM cs_users
         WHERE ulogin='%s' AND utoken='%s'",
         $dbconn->real_escape_string($user),
         $dbconn->real_escape_string($token));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $token = $row['utoken'];
        $user = $row['ulogin'];
        $profileid = $row['uid'];
    }
    $result->close();
}

if (!isset($token) || !isset($user)) {
    redirect_to('auth/login.php');
}

if (isset($_POST['coffeetime']) && !empty($_POST['coffeetime'])) {
    // TODO: add proper input validation (see https://bugs.n0q.org/view.php?id=13)
    $coffeedate = AntiXSS::setFilter($_POST['coffeetime'], "whitelist", "string");
    // TODO: implement coffee registration in a function (see https://bugs.n0q.org/view.php?id=27)
    $sql = sprintf(
        "SELECT cid, cdate
         FROM cs_coffees
         WHERE cdate > (NOW() - INTERVAL '5:00' MINUTE_SECOND)
           AND (NOW() + INTERVAL '45:00' MINUTE_SECOND) > (cdate + INTERVAL '45' MINUTE_SECOND)
           AND cuid = %d",
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $result->close();
        flash(sprintf(
            'Error: Your last coffee was at least not 5 minutes ago at %s. O_o',
            $row['cdate']),
            FLASH_WARNING);
    }
    else {
        $result->close();
        $sql = sprintf(
            "INSERT INTO cs_coffees (cuid, cdate)
             VALUES (%d, '%s')",
            $profileid, $dbconn->real_escape_string($coffeedate));
        if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
            handle_mysql_error();
        }
        flash(sprintf(
            'Your coffee at %s has been registered!', $coffeedate),
            FLASH_SUCCESS);
    }
}
elseif (isset($_POST['matetime']) && !empty($_POST['matetime'])) {
    // TODO: add proper input validation (see https://bugs.n0q.org/view.php?id=13)
    $matedate = AntiXSS::setFilter($_POST['matetime'], "whitelist", "string");
    // TODO: implement coffee registration in a function (see https://bugs.n0q.org/view.php?id=27)
    $sql = sprintf(
        "SELECT mid, mdate
         FROM cs_mate
         WHERE mdate > (NOW() - INTERVAL '5:00' MINUTE_SECOND)
           AND (NOW() + INTERVAL '45:00' MINUTE_SECOND) > (mdate + INTERVAL '45' MINUTE_SECOND)
           AND cuid = %d",
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $result->close();
        flash(sprintf(
            "Error: Your last mate was at least not 5 minutes ago at %s. O_o",
            $row['mdate']),
            FLASH_WARNING);
    }
    else {
        $result->close();
        $sql=sprintf(
            "INSERT INTO cs_mate (cuid, mdate)
             VALUES (%d, '%s')",
            $profileid, $dbconn->real_escape_string($matedate));
        if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
            handle_mysql_error();
        }
        flash(sprintf(
            'Your mate at %s has been registered!', $matedate),
            FLASH_SUCCESS);
    }
}

include("header.php");
// simplify JavaScript code (see https://bugs.n0q.org/view.php?id=28)
?>
<script type="text/javascript">
function pad(n) {
    return n<10 ? '0'+n : n;
}
function AddPostDataCoffee() {
    function coffeetime(d) {
        return d.getFullYear() + '-' +
           pad(d.getMonth() + 1) +'-' +
           pad(d.getDate()) + ' ' +
           pad(d.getHours()) + ':' +
           pad(d.getMinutes()) +':' +
           pad(d.getSeconds());
    }
    var d = new Date();
    document.getElementById('coffeetime').value = coffeetime(d);
    document.getElementById("coffeeform").submit();
}
function AddPostDataMate() {
    function coffeetime(d) {
        return d.getFullYear() + '-' +
            pad(d.getMonth() + 1) + '-' +
            pad(d.getDate()) + ' ' +
            pad(d.getHours()) + ':' +
            pad(d.getMinutes()) + ':' +
            pad(d.getSeconds());
    }
    var d = new Date();
    document.getElementById('matetime').value = coffeetime(d);
    document.getElementById("mateform").submit();
}
</script>

<div class="white-box">
    <h2>On the run?</h2>
    <center>
        <form action="" method="post" id="coffeeform">
            <input class="imadecoffee" type="submit" value="Coffee!" id="coffee_plus_one" onclick="AddPostDataCoffee();" /><br />
            <input type='hidden' id='coffeetime' name='coffeetime' value='' />
        </form>
        <form action="" method="post" id="mateform">
            <input class="imademate" type="submit" value="Mate!" id="coffee_plus_one" onclick="AddPostDataMate();" /><br />
            <input type='hidden' id='matetime' name='matetime' value='' />
        </form>
    </center>
</div>
<?php
include('footer.php');
?>
