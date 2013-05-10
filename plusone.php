<?php
include('auth/lock.php');
include_once('includes/common.php');
include_once('includes/validation.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['coffeetime']) && validate_datetime($_POST['coffeetime'])) {
        register_coffee($_SESSION['login_id'], $_POST['coffeetime']);
    }
    elseif (isset($_POST['matetime']) && validate_datetime($_POST['matetime'])) {
        register_mate($_SESSION['login_id'], $_POST['matetime']);
    }
    else {
        errorpage(
            'Bad Request',
            'Your request contained bad data',
            '400 Bad Request');
    }
}
include("header.php");
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
    var timefield = document.getElementById('coffeetime');
    if (timefield.value.length == 0) {
        timefield.value = coffeetime(d);
    }
    document.getElementById('coffeeform').submit();
}

function AddPostDataMate() {
    var d = new Date();
    var timefield = document.getElementById('matetime');
    if (timefield.value.length == 0) {
        timefield.value = coffeetime(d);
    }
    document.getElementById('mateform').submit();
}

function toggle(control){
    var elem = document.getElementById(control);

    if (elem.style.display == "none") {
        elem.style.display = "inline";
    }
    else {
        elem.style.display = "none";
    }
}
</script>

<div class="white-box">
    <h2>Coffee?</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="coffeeform" onsubmit="return false;">
        <div>
            <a href="javascript:toggle('specdate')"><img src="./images/revert.png"></a>
            <input class="imadecoffee" type="submit" value="Coffee!" id="coffee_plus_one" onclick="AddPostDataCoffee();" />
            <div id="specdate" style="display: none">
                <input type="text" id="coffeetime" name="coffeetime" placeholder="<?php echo date('Y-m-d H:i', time()); ?>" class="default_input_field datetime_field" />
            </div>
        </div>
    </form>
</div>

<div class="white-box">
    <h2>Mate?</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="mateform" onsubmit="return false;">
        <div>
            <a href="javascript:toggle('specdatem')"><img src="./images/revert.png"></a>
            <input class="imademate" type="submit" value="Mate!" id="coffee_plus_one" onclick="AddPostDataMate();" />
            <div id="specdatem" style="display: none">
                <input type="text" id="matetime" name="matetime" placeholder="<?php echo date('Y-m-d H:i', time()); ?>" class="default_input_field datetime_field" />
            </div>
        </div>
    </form>
</div>
<?php
include('footer.php');
?>
