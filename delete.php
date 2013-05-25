<?php
include('auth/lock.php');
include_once('includes/common.php');
include_once('includes/queries.php');

if (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['cid'])) {
    $cid = intval($_POST['cid']);
    if (delete_caffeine_entry($cid, $_SESSION['login_id'])) {
        flash('Entry deleted successfully!', FLASH_SUCCESS);
    }
    redirect_to('profile');
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
<div class="white-box fullWidth">
    <h2>Confirm deletion</h2>
    <p><?php
printf(
    "Do you really want to delete your %s entry at %s%s",
    get_entrytype($entry['ctype']), $entry['cdate'],
    format_timezone($entry['ctimezone']));
?>?</p>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="inlineform">
        <input type="submit" name="submit" value="Yes!" class="left" />
        <a href="profile" class="btn secondary left">No, cancel</a>
        <input type="hidden" name="cid" value="<?php echo $entry['cid']; ?>" />
    </form>
</div>
<?php
include('footer.php');
?>
