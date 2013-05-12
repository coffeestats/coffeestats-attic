<?php
// TODO: do some real comparison (see https://bugs.n0q.org/view.php?id=23)

include("auth/lock.php");
include_once("includes/common.php");
include_once("includes/validation.php");
include_once("includes/queries.php");

// Parse user
if (isset($_GET['u'])) {
    if (($profileuser = sanitize_username($_GET['u'])) === FALSE) {
        errorpage('Error', 'Invalid username.', '400 Bad Request');
    }
}
if (!isset($profileuser)) {
    errorpage('Error', 'Invalid request!', '400 Bad Request');
}

$sql = sprintf(
    "SELECT uid FROM cs_users WHERE ulogin='%s'",
    $dbconn->real_escape_string($profileuser));
if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
    handle_mysql_error();
}
if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $profileid = $row['uid'];
}
$result->close();

// TODO: handle similarly to profile.php
if (isset($profileid)) {
    if ($profileid == $_SESSION['login_id']) {
        $headline = 'Your Profile';
    }
    else {
        $headline = sprintf("%s's Profile", htmlspecialchars($profileuser));
    }
}
else {
    errorpage('Profile not found', 'No user with the given username exists.', '404 Not Found');
}

// total
$total = total_caffeine_for_profile($profileid);

/**
 * print an associative array with a specified element count for a chart row.
 */
function chartrows($data) {
    foreach ($data as $label => $value) {
        printf("['%s',%d],", $label, $value[0]);
    }
}

// queries for charts

// today
$hourrows = hourly_caffeine_for_profile($profileid);

// month
$dayrows = daily_caffeine_for_profile($profileid);

// year
$monthrows = monthly_caffeine_for_profile($profileid);

include("header.php");
?>
<div class="white-box">
  <h2><?php echo $headline; ?></h2>
  Coffees total: <?php echo $total['coffees']; ?>
</div>
<div class="white-box">
  <div id="coffee_today"></div>
</div>
<div class="white-box">
  <div id="coffee_month"></div>
</div>
<div class="white-box">
  <div id="coffee_year"></div>
</div>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["corechart"]});
    google.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Hour');
        data.addColumn('number', 'Coffees');
        data.addRows([<?php chartrows($hourrows); ?>]);

        var options = {
            width: 550, height: 240,
            title: 'Your coffees today',
            hAxis: {title: 'Hour'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('coffee_today'));
        chart.draw(data, options);

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Month');
        data.addColumn('number', 'Coffees');
        data.addRows([<?php chartrows($dayrows); ?>]);

        var options = {
          width: 550, height: 240,
          title: 'Your coffees this month',
          hAxis: {title: 'Day'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('coffee_month'));
        chart.draw(data, options);

        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Day');
        data.addColumn('number', 'Coffees');
        data.addRows([<?php chartrows($monthrows); ?>]);

        var options = {
          width: 550, height: 240,
          title: 'Your coffees this year',
          hAxis: {title: 'Year'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('coffee_year'));
        chart.draw(data, options);
    }
</script>

<?php
include("footer.php");
?>
