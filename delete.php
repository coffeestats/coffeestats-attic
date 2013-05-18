<?php
include('auth/lock.php');
include_once('includes/common.php');
include_once('includes/queries.php');

if (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['cid'])) {
    $cid = intval($_POST['cid']);
    if (delete_caffeine_entry($cid, $_SESSION['login_id'])) {
        flash('Entry deleted successfully!', FLASH_SUCCESS);
    }
    redirect_to('plusone');
}
elseif (isset($_GET['c']) && isset($_SESSION['login_id']))
{
    $cid = intval($_GET['c']);
    $entry = fetch_entry($cid, $_SESSION['login_id']);
    if ($entry === NULL) {
        errorpage(
            'No such entry', 'The given entry could not be found!',
            '404 Not Found');
    }
}
else {
    errorpage('Bad request', 'Invalid request data.', '400 Bad Request');
}

include('header.php');
?>
<div class="white-box">
    <h2>Confirm deletion</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="inlineform">
        <input type="hidden" name="cid" value="<?php echo $entry['cid']; ?>" />
        <p><?php
printf(
    "Do you really want to delete your %s entry at %s%s",
    get_entrytype($entry['ctype']), $entry['cdate'],
    format_timezone($entry['ctimezone']));
?>?</p>
        <p><input type="submit" name="submit" value="Yes!" /> <a href="plusone">No, cancel</a></p>
    </form>
</div>
<?php
include('footer.php');
?>
