<?php
include("auth/lock.php");
include_once('includes/common.php');

// Export Function
function export_csv($type) {
    global $dbconn;
    // TODO: use an absolute base path from configuration for export files
    switch ($type) {
    case 'coffee':
        $file = tempnam(sys_get_temp_dir(), sprintf('coffees-%s', $_SESSION['login_user']));
        $sql = sprintf(
            "SELECT cdate AS thedate
             FROM cs_coffees
             WHERE cuid = %d",
            $dbconn->real_escape_string($_SESSION['login_id']));
        break;
    case 'mate':
        $file = tempnam(sys_get_temp_dir(), sprintf('mate-%s', $_SESSION['login_user']));
        $sql = sprintf(
            "SELECT mdate AS thedate
             FROM cs_mate
             WHERE cuid = %d",
            $dbconn->real_escape_string($_SESSION['login_id']));
        break;
    default:
        error_log(sprintf('Invalid call to export_csv with type %s', $type));
        return NULL;
    }
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    if (($csvfile = fopen($file, 'w')) !== FALSE) {
        fputcsv($csvfile, array("Timestamp"));
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            fputcsv($csvfile, array($row['thedate']));
        }
        $result->close();
        fclose($csvfile);
        return $file;
    }
    else {
        flash("Problem during export", FLASH_ERROR);
        return NULL;
    }

}

// Switching between modes
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
    case 'export':
        $coffeefile = export_csv('coffee');
        $matefile = export_csv('mate');
        // TODO: mail CSV files to profile email address
        flash('Your data has been exported. You will receive an email with two CSV files with your coffee and mate registrations attached.', FLASH_INFO);
        break;
    case 'update':
        flash('Updating ...', FLASH_INFO);
        break;
    case 'delete':
        flash('Deleting ...', FLASH_INFO);
        break;
    default:
        error_log(sprintf('Invalid call wrong POST action %s', $_POST['action']));
        errorpage('Bad request', 'Your request contained an invalid action', '400 Bad Request');
    }
}

include("header.php");
?>
<div class="white-box">
    <h2>Update your profile</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="coffeeform">
    <input type="hidden" name="action" value="update" />
    <p>
        <b>General</b><br/>
        <input type="text" name="Login" maxlength="20" placeholder="Username" readonly="readonly" />
        <input type="password" name="Password" maxlength="20" placeholder="Password" />
        <input type="text" name="Email" maxlength="50" placeholder="E-Mail" />
    </p>
    <p>
        <b>Additional</b><br/>
        <input type="text" name="Forename" maxlength="20" placeholder="Forename" />
        <input type="text" name="Name" maxlength="20" placeholder="Name" />
        <input type="text" name="Location" maxlength="20" placeholder="Location" />
    </p>
    <p><input type="submit" name="submit" value="Update my settings" /></p>
    </form>
</div>

<div class="white-box">
    <h2>Export your Activity</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="coffeeform">
    <input type="hidden" name="action" value="export" />
    <p>Your data is yours. You will recieve the csv files via mail.</p>
    <p><input type="submit" name="submit" value="Export, please!" /></p>
    </form>
</div>

<div class="white-box">
    <h2>Delete your data</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="coffeeform">
    <input type="hidden" name="action" value="delete" />
    <p>Your data is yours. So it is your decission to leave us.</p>
    <p><input type="submit" name="submit" value="Delete me, please!" /></p>
    </form>
</div>
<?php
include("footer.php");
?>
