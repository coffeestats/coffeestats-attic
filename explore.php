<?php
include("auth/lock.php");
include_once('includes/common.php');
include_once('includes/queries.php');

$activities = latest_caffeine_activity(10);
$users = random_users(4);
$topcoffee = top_caffeine_consumers_total(10);
$topcoffeeavg = top_caffeine_consumers_average(10);
$recentlyjoined = recently_joined_users(5);

include("header.php");
?>
<div class="white-box">
    <h2>Explore!</h2>
    <p>You're not the only human at this site! Great, isn't it? Lets see the stats of some other guys.</p>
</div>
<div class="white-box">
    <h2>Caffeine Activity</h2>
    <ul class="userlist">
<?php foreach ($activities as $activity) { ?>
        <li><?php echo profilelink($activity['ulogin']); ?>
<?php printf(' %s at %s', get_entrytype($activity['label']), $activity['date']); ?>
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
</div>
<div class="white-box">
    <h2>Ranking</h2>
    <table  width=500 height=200>
        <tr>
            <td width=50%>
            Coffees Summary
            <ul>
<?php foreach ($topcoffee as $user) { ?>
                <li><?php echo profilelink($user['ulogin']); ?> - <?php echo $user['total']; ?> Coffees</li>
<?php } ?>
            </ul>
            </td>
            <td width=50%>
            Average Coffees a day
            <ul>
<?php foreach ($topcoffeeavg as $user) { ?>
                <li><?php echo profilelink($user['ulogin']); ?> - <?php echo $user['average']; ?></li>
<?php } ?>
            </ul>
            </td>
        </table>
    </ul>
</div>
<div class="white-box">
    <h2>Recently registered</h2>
    <ul>
<?php foreach ($recentlyjoined as $user) { ?>
        <li><?php echo profilelink($user['ulogin']); ?></li>
<?php } ?>
    </ul>
</div>
<?php
include("footer.php");
?>
