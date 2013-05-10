<?php
include('auth/config.php');
include('lib/antixss.php');
include_once('includes/common.php');
include_once('includes/validation.php');

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['coffeetime']) && validate_datetime($_POST['coffeetime'])) {
        register_coffee($profileid, $_POST['coffeetime']);
    }
    elseif (isset($_POST['matetime']) && validate_datetime($_POST['matetime'])) {
        register_mate($profileid, $_POST['matetime']);
    }
}

include("header.php");
// simplify JavaScript code (see https://bugs.n0q.org/view.php?id=28)
?>
<script type="text/javascript">
function pad(n) {
    return n<10 ? '0'+n : n;
}
function coffeetime(d) {
    return d.getFullYear() + '-' +
       pad(d.getMonth() + 1) +'-' +
       pad(d.getDate()) + ' ' +
       pad(d.getHours()) + ':' +
       pad(d.getMinutes()) +':' +
       pad(d.getSeconds());
}
function AddPostDataCoffee() {
    var d = new Date();
    document.getElementById('coffeetime').value = coffeetime(d);
    document.getElementById("coffeeform").submit();
}
function AddPostDataMate() {
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
