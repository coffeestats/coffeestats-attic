<?php
include("auth/lock.php");
include_once('includes/common.php');
include_once('includes/queries.php');

$activities = latest_caffeine_activity(10);
$users = random_users(4);
$topcoffee = top_caffeine_consumers_total(10, 0);
$topcoffeeavg = top_caffeine_consumers_average(10, 0);
$topmate = top_caffeine_consumers_total(10, 1);
$topmateavg = top_caffeine_consumers_average(10, 1);
$recentlyjoined = recently_joined_users(5);
$longestjoined = longest_joined_users(5);

include("header.php");
?>
<div class="white-box fullWidth">
    <h2>Explore!</h2>
    <p>You're not the only human at this site! Great, isn't it? Lets see the stats of some other guys.</p>
</div>
<div><!--space--></div>
<div class="white-box">
    <h2>Caffeine Activity</h2>
    <ul class="userlist">
<?php foreach ($activities as $activity) { ?>
        <li><?php echo profilelink($activity['ulogin']); ?>
<?php printf(' %s at %s%s', get_entrytype($activity['ctype']), $activity['cdate'], format_timezone($activity['ctimezone'])); ?>
</li>
<?php } ?>
    </ul>
</div>
<div class="white-box">
    <h2>Get in touch with eachother!</h2>
    <div id="random_users">
<?php foreach ($users as $user) { ?>
        <div class="usercard">
            <?php echo profilelink($user['ulogin']); ?><br />
            Name: <?php printf("%s %s", $user['ufname'], $user['uname']); ?><br />
            Location: <?php echo $user['ulocation']; ?><br />
            Coffees total: <?php echo $user['coffees']; ?><br />
            Mate total: <?php echo $user['mate']; ?>
        </div>
<?php } ?>
    </div>
    <div class="clearfix">&nbsp;</div>
    <p><a href="#" id="more-users">More users</a></p>
</div>
<div class="white-box" id="ranks">
    <h2>Caffeine Ranking</h2>
    <div class="rankbox">
        <h3>Top coffee drinkers</h3>
        <ol class="ranklist">
<?php foreach ($topcoffee as $user) { ?>
            <li><?php echo profilelink($user['ulogin']); ?> - <?php echo $user['total']; ?> Coffees</li>
<?php } ?>
        </ol>
    </div>
    <div class="rankbox">
        <h3>Top mate drinkers</h3>
        <ol class="ranklist">
<?php foreach ($topmate as $user) { ?>
            <li><?php echo profilelink($user['ulogin']); ?> - <?php echo $user['total']; ?> Mate</li>
<?php } ?>
        </ol>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div class="rankbox">
        <h3>Top daily coffee average</h3>
        <ol class="ranklist">
<?php foreach ($topcoffeeavg as $user) { ?>
                <li><?php echo profilelink($user['ulogin']); ?> - <?php echo $user['average']; ?></li>
<?php } ?>
        </ol>
    </div>
    <div class="rankbox">
        <h3>Top daily mate average</h3>
        <ol class="ranklist">
<?php foreach ($topmateavg as $user) { ?>
                <li><?php echo profilelink($user['ulogin']); ?> - <?php echo $user['average']; ?></li>
<?php } ?>
        </ol>
    </div>
    <div class="clearfix">&nbsp;</div>
</div>
<div class="white-box">
    <h2>Newest vs. Oldest members</h2>
    <div class="rankbox">
        <h3>Joined recently</h3>
        <ul class="userlist">
<?php foreach ($recentlyjoined as $user) { ?>
            <li><?php echo profilelink($user['ulogin']); ?> (<?php echo $user['days']; ?> days)</li>
<?php } ?>
        </ul>
    </div>
    <div class="rankbox">
        <h3>Veterans</h3>
        <ul class="userlist">
<?php foreach($longestjoined as $user) { ?>
            <li><?php echo profilelink($user['ulogin']); ?> (<?php echo $user['days']; ?> days)</li>
<?php } ?>
        </ul>
    </div>
    <div class="clearfix">&nbsp;</div>
</div>
<script type="text/javascript" src="lib/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#more-users').click(function(event) {
        $.getJSON('<?php echo baseurl(); ?>/api/v1/random-users?count=4', function(data) {
            $('#random_users > div').remove();
            for (var i=0; i < data.length; i++) {
                $('#random_users').append(
                    '<div class="usercard"><a href="' + data[i].profile + '">' + data[i].username + '</a><br />' +
                    'Name: ' + data[i].name + '<br />' +
                    'Locaton: ' + data[i].location + '<br />' +
                    'Coffees total: ' + data[i].coffees + '<br />' +
                    'Mate total: ' + data[i].mate + '</div>');
            }
        });
        return false;
    });
});
</script>
<?php
include("footer.php");
?>
