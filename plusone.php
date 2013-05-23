<?php
include('auth/lock.php');
include_once('includes/common.php');
include_once('includes/queries.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once('includes/validation.php');

    if (isset($_POST['coffeetime']) && (($coffeetime = sanitize_datetime($_POST['coffeetime'])) !== FALSE)) {
        register_coffee($_SESSION['login_id'], $coffeetime, $_SESSION['timezone']);
    }
    elseif (isset($_POST['matetime']) && (($matetime = sanitize_datetime($_POST['matetime'])) !== FALSE)) {
        register_mate($_SESSION['login_id'], $matetime, $_SESSION['timezone']);
    }
    else {
        errorpage(
            'Bad Request',
            'Your request contained bad data',
            '400 Bad Request');
    }
    redirect_to($_SERVER['REQUEST_URI']);
}

$entries = latest_entries($_SESSION['login_id']);

include_once('includes/jsvalidation.php');
include("header.php");
?>
<div class="white-box">
    <h2>Coffee or Mate?</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="coffeeform" class="inlineform">
        <div>
            <input type="submit" value="Coffee!" />
            <input type="text" id="coffeetime" name="coffeetime" placeholder="<?php echo date('Y-m-d H:i', time()); ?>" class="datetime_field" />
        </div>
    </form>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="mateform" class="inlineform">
        <div>
            <input type="submit" value="Mate!" />
            <input type="text" id="matetime" name="matetime" placeholder="<?php echo date('Y-m-d H:i', time()); ?>" class="datetime_field" />
        </div>
    </form>
</div>

<?php if (count($entries) > 0): ?>
<div class="white-box">
    <h2>Your latest entries</h2>
    <table>
        <?php foreach ($entries as $entry) { ?>
        <tr><td><?php
printf(
    "%s at %s%s",
    get_entrytype($entry['ctype']), $entry['cdate'],
    format_timezone($entry['ctimezone']));
?></td><td><a href="delete?c=<?php echo $entry['cid']; ?>" data-cid="<?php echo $entry['cid']; ?>" class="deletecaffeine"> <img src="images/nope.png"></a></td></tr>
        <?php } ?>
    </table>
</div>
<?php endif; ?>
<script type="text/javascript" src="../lib/jquery.min.js"></script>
<?php js_sanitize_datetime(); ?>
<script type="text/javascript">
$(document).ready(function() {
    $('img.toggle').click(function(event) {
        $('#' + $(this).attr('data-toggle')).toggle();
    });
    $('#coffeeform').submit(function(event) {
        return sanitize_datetime('input#coffeetime');
    });
    $('#mateform').submit(function(event) {
        return sanitize_datetime('input#matetime');
    });
});
</script>
<?php
include('footer.php');
?>
