<?php
include("auth/lock.php");
include_once("includes/common.php");

$tzlist = timezone_identifiers_list();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['timezone']) && in_array($_POST['timezone'], $tzlist)) {
        include_once('includes/queries.php');
        set_user_timezone($_SESSION['login_id'], $_POST['timezone']);
        flash(
            sprintf(
                'Your time zone has been set to %s successfully.',
                $_POST['timezone']),
            FLASH_SUCCESS);
        $_SESSION['timezone'] = $_POST['timezone'];
        redirect_to('../index');
    }
    else {
        errorpage('Bad Request', 'Input data is not as expected.', '400 Bad Request');
    }
}

include("header.php");
?>
<div class="white-box">
    <p>Detected timezone: <span id="timezoneinfo">Not detected yet</span></p>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="inlineform">
        <p>
            <select name="timezone" id="tzselect">
<?php foreach($tzlist as $tz) { ?>
                <option><?php echo $tz; ?></option>
<?php } ?>
            </select>
            <input type="submit" name="submit" value="Select timezone" />
        </p>
    </form>
</div>
<script type="text/javascript" src="lib/jquery.min.js"></script>
<script type="text/javascript" src="lib/jstz-1.0.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var tz = jstz.determine();
    var tzname = tz.name();
    $('#timezoneinfo').text(tzname);
    var tzselect = $('#tzselect');
    for (var i=0; i < tzselect[0].options.length; i++) {
        if (tzselect[0].options[i].text === tzname) {
            tzselect[0].selectedIndex = i;
        }
    }
});
</script>
<?php
include("footer.php");
?>
