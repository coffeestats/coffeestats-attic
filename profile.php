<?php
session_start();
if (isset($_SESSION['login_user'])) {
    include("auth/lock.php");
}
include_once("includes/common.php");
include_once("includes/validation.php");
include_once("includes/queries.php");

$ownprofile = FALSE;
// Parse user
if (isset($_GET['u'])) {
    if (($profileuser = sanitize_username($_GET['u'])) === FALSE) {
        errorpage('Error', 'Invalid username.', '400 Bad Request');
    }
}
elseif (isset($_SESSION['login_user'])) {
    $ownprofile = TRUE;
    $profileuser = $_SESSION['login_user'];
}
else {
    // not logged in and no profile user specified
    errorpage('Error', 'Invalid request!', '400 Bad Request');
}

if (($userinfo = find_user_by_login($profileuser)) === NULL) {
    // no result found
    errorpage('Error', 'No profile found', '404 No Profile Found');
}

$profileid = $userinfo['uid'];
$profilename = $userinfo['uname'];
$profileforename = $userinfo['ufname'];
$profilelocation = $userinfo['ulocation'];
$profiletoken = $userinfo['utoken'];

$total = total_caffeine_for_profile($profileid);

if ($ownprofile) {
    $public_url = public_url($profileuser);
    $otr_url = on_the_run_url($profileuser, $profiletoken);
    $info = array(
        'title' => 'Your Profile',
        'data' => array(
            'Name' => sprintf(
                '%s %s', htmlspecialchars($profileforename),
                htmlspecialchars($profilename)),
            'Location' => htmlspecialchars($profilelocation),
            'Your Coffees total' => $total['coffees'],
            'Your Mate total' => $total['mate'],
        ),
        'extra' => array(
            sprintf('<a class="btn secondary" href="%s">public profile page</a>', $public_url),
            sprintf('<a class="btn secondary" href="%s">on-the-run</a>', $otr_url),
        ),
        'afterlist' => sprintf(
            '<a title="share your public profile page on facebook" href="https://www.facebook.com/sharer.php?u=%1$s&t=My%%20coffee%%20statistics">' .
            '<img src="images/facebook40.png" alt="facebook share icon" /></a> ' .
            '<a title="share your public profile page on twitter" href="https://twitter.com/intent/tweet?original_referer=%1$s&text=My%%20coffee%%20statistics&tw_p=tweetbutton&url=%1$s&via=coffeestats">' .
            '<img src="images/twitter40.png" alt="twitter share" /></a> ' .
            '<a title="share your public profile page on gplus" href="https://plus.google.com/share?url=%1$s">' .
            '<img src="images/googleplus40.png" alt="google plus share" /></a>',
            urlencode(public_url($profileuser))),
    );
}
else {
    $info = array(
        'title' => sprintf("%s's Profile", htmlspecialchars($profileuser)),
        'data' => array(
            'Name' => sprintf(
                '%s %s', htmlspecialchars($profileforename),
                htmlspecialchars($profilename)),
            'Location' => htmlspecialchars($profilelocation),
            'Coffees total' => $total['coffees'],
            'Mate total' => $total['mate'],
        ),
    );
}

$todayrows = hourly_caffeine_for_profile($profileid);
$monthrows = daily_caffeine_for_profile($profileid);
$yearrows = monthly_caffeine_for_profile($profileid);
$byhourrows = hourly_caffeine_for_profile_overall($profileid);
$byweekdayrows = weekdaily_caffeine_for_profile_overall($profileid);

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
<?php if ($ownprofile) { ?>
     <div class="rightfloated">
        <canvas id="ontherunqrcode" width="100" height="100"></canvas>
    </div>
<?php } ?>
    <h2><?php echo $info['title']; ?></h2>
    <ul class="profilelist">
<?php
foreach ($info['data'] as $key => $value) { ?>
        <li><?php echo $key; ?>: <?php echo $value; ?></li>
<?php
}
?>

    </ul>
<div class="pagelinks">
    <div class="left">
        <?php
            if (isset($info['extra'])) {
                foreach ($info['extra'] as $value) { ?>
                    <?php echo $value; ?>
            <?php
                }
            }
        ?>
    </div>
    <div class="right">
        <?php
            if (isset($info['afterlist'])) {
                echo $info['afterlist'];
            }
        ?>
    </div>
</div>
</div>
<?php if ($ownprofile) { ?>
<div class="white-box update">
    <h2>Coffee or Mate?</h2>
    <?php render_flash('registerdrink'); ?>
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
<?php endif;
} ?>
<div class="white-box today">
    <h2>Caffeine today</h2>
    <canvas id="coffeetoday" width="590" height="240" ></canvas>
</div>
<div class="white-box">
    <h2>Caffeine this month</h2>
    <canvas id="coffeemonth" width="590" height="240" ></canvas>
</div>
<div class="white-box">
    <h2>Coffees vs. Mate</h2>
    <canvas id="coffeevsmate" width="590" height="240" ></canvas>
</div>
<div class="white-box">
    <h2>Caffeine this year</h2>
    <canvas id="coffeeyear" width="590" height="240" ></canvas>
</div>
<div class="white-box">
    <h2>Caffeine by hour (overall)</h2>
    <canvas id="coffeebyhour" width="590" height="240" ></canvas>
</div>
<div class="white-box">
    <h2>Caffeine by weekday (overall)</h2>
    <canvas id="coffeebyweekday" width="590" height="240" ></canvas>
</div>

<?php if ($ownprofile) { ?>
<script type="text/javascript" src="lib/jsqr-0.2-min.js"></script>
<script type="text/javascript">
function drawQR(data, canvasid) {
    var qr = new JSQR();
    var code = new qr.Code();

    code.encodeMode = code.ENCODE_MODE.BYTE;
    code.version = code.DEFAULT;
    code.errorCorrection = code.ERROR_CORRECTION.H;

    var input = new qr.Input();
    input.dataType = input.DATA_TYPE.TEXT;
    input.data = data;

    var matrix = new qr.Matrix(input, code);
    matrix.scale = 3;
    matrix.margin = 2;

    var canvas = document.getElementById(canvasid);
    canvas.setAttribute('width', matrix.pixelWidth);
    canvas.setAttribute('height', matrix.pixelWidth);
    canvas.getContext('2d').fillStyle = 'rgb(0,0,0)';
    matrix.draw(canvas, 0, 0);
}

drawQR('<?php echo $otr_url; ?>', 'ontherunqrcode');
</script>
<?php }
include('includes/charting.php');
?>
<script type="text/javascript">
var todaycolor = "#E64545";
var monthcolor = "#FF9900";
var yearcolor = "#3399FF";
var hourcolor = "#FF6666";
var weekdaycolor = "#A3CC52";
var matecolor = "#FFCC00";
var matelightcolor = "#FFE066";
var barChartData;
var lineChartData;

var doughnutData = [
    {
        value: <?php echo($total['coffees']); ?>,
        color: todaycolor
    },
    {
        value: <?php echo($total['mate']); ?>,
        color: matecolor
    }
];
new Chart(document.getElementById("coffeevsmate").getContext("2d")).Doughnut(doughnutData);

barChartData = {
    labels: [<?php extractlabels($todayrows); ?>],
    datasets: [
        {
            fillColor: todaycolor,
            strokeColor: todaycolor,
            data: [<?php extractdata($todayrows, 0); ?>],
        },
        {
            fillColor: matecolor,
            strokeColor: matecolor,
            data: [<?php extractdata($todayrows, 1); ?>],
        },
    ]
}
drawBarChart('coffeetoday', barChartData, <?php scalesteps($todayrows); ?>);

lineChartData = {
    labels: [<?php extractlabels($monthrows); ?>],
    datasets : [
        {
            fillColor: monthcolor,
            strokeColor: "#FFB84D",
            pointColor: "#FFB84D",
            pointStrokeColor: "#fff",
            data: [<?php extractdata($monthrows, 0); ?>],
        },
        {
            fillColor: matecolor,
            strokeColor: matelightcolor,
            pointColor: matelightcolor,
            pointStrokeColor: "#fff",
            data: [<?php extractdata($monthrows, 1); ?>],
        },
    ]
}
drawLineChart('coffeemonth', lineChartData, <?php scalesteps($monthrows); ?>);

barChartData = {
    labels: [<?php extractlabels($yearrows); ?>],
    datasets: [
        {
            fillColor: yearcolor,
            strokeColor: yearcolor,
            data: [<?php extractdata($yearrows, 0); ?>],
        },
        {
            fillColor: matecolor,
            strokeColor : matecolor,
            data: [<?php extractdata($yearrows, 1); ?>],
        },
    ]
}
drawBarChart('coffeeyear', barChartData, <?php scalesteps($yearrows); ?>);

lineChartData = {
    labels: [<?php extractlabels($byhourrows); ?>],
    datasets: [
        {
            fillColor: hourcolor,
            strokeColor: "#FF9999",
            pointColor: "#FF9999",
            pointStrokeColor: "#fff",
            data: [<?php extractdata($byhourrows, 0); ?>],
        },
        {
            fillColor: matecolor,
            strokeColor: matelightcolor,
            pointColor: matelightcolor,
            pointStrokeColor: "#fff",
            data: [<?php extractdata($byhourrows, 1); ?>],
        },
    ]
}
drawLineChart('coffeebyhour', lineChartData, <?php scalesteps($byhourrows); ?>);

lineChartData = {
    labels: [<?php extractlabels($byweekdayrows); ?>],
    datasets: [
        {
            fillColor: weekdaycolor,
            strokeColor: "#99FF99",
            pointColor: "#99FF99",
            pointStrokeColor: "#fff",
            data: [<?php extractdata($byweekdayrows, 0); ?>],
        },
        {
            fillColor: matecolor,
            strokeColor: matelightcolor,
            pointColor: matelightcolor,
            pointStrokeColor: "#fff",
            data: [<?php extractdata($byweekdayrows, 1); ?>],
        },
    ]
}
drawLineChart('coffeebyweekday', lineChartData, <?php scalesteps($byweekdayrows); ?>);
</script>
<script type="text/javascript" src="lib/jquery.min.js"></script>
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
include("footer.php");
?>
