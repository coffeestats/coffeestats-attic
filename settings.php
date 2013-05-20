<?php
include("auth/lock.php");
include_once('includes/common.php');

// Export Function
function export_csv() {
    global $dbconn;
    // TODO: use an absolute base path from configuration for export files
    $file = tempnam(sys_get_temp_dir(), sprintf('caffeine-%s', $_SESSION['login_user']));
    $sql = sprintf(
         "SELECT cdate AS thedate
          FROM cs_caffeine
          WHERE cuid = %d",
         $dbconn->real_escape_string($_SESSION['login_id']));
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

function update_user($uuserid) {
    global $dbconn;
    if (!isset($_POST['email']) ||
        !isset($_POST['password']) ||
        !isset($_POST['password2']) ||
        !isset($_POST['firstname']) ||
        !isset($_POST['lastname']) ||
        !isset($_POST['location']))
    {
        errorpage('Bad request', 'The request is invalid.', '400 Bad Request');
    }

    include_once('includes/validation.php');

    $email = sanitize_email($_POST['email']);
    $password = sanitize_password($_POST['password'], $_POST['password2']);
    $firstname = sanitize_string($_POST['firstname'], FALSE);
    $lastname = sanitize_string($_POST['lastname'], FALSE);
    $location = sanitize_string($_POST['location'], FALSE);

    if (($email !== FALSE) &&
        ($password !== FALSE) && ($firstname !== FALSE) &&
        ($lastname !== FALSE) && ($location !== FALSE))
    {
      $password = hash_password($password);

      $sql = sprintf(
             "UPDATE cs_users SET 
              uemail='%s', ufname='%s', uname='%s', ucryptsum='%s',
              ulocation='%s' 
              WHERE uid = %d",
            $dbconn->real_escape_string($email),
            $dbconn->real_escape_string($firstname),
            $dbconn->real_escape_string($lastname),
            $dbconn->real_escape_string($password),
            $dbconn->real_escape_string($location),
            $dbconn->real_escape_string($uuserid));
      if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
      }
      flash("Successfully updated your profile informations!", FLASH_SUCCESS);
    }
}



// Switching between modes
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
    case 'export':
        $caffeinefile = export_csv();
        multi_attach_mail($, $files, $sendermail)
        flash('Your data has been exported. You will receive an email with two CSV files with your coffee and mate registrations attached.', FLASH_INFO);
        break;
    case 'update':
        flash($_SESSION['login_id'], FLASH_INFO);
        update_user($_SESSION['login_id']);
        break;
    case 'delete':
      flash('We recieved your deletion request. Your account will be deleted with all its information within the next week.
             If you change your mind, feel free to mail us at '.get_setting(SITE_ADMINMAIL), FLASH_INFO);
        send_user_deletion($_SESSION['login_user'],$_SESSION['login_id']);
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
    <p>Please fill in all your information, including your password.</p>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="coffeeform">
    <input type="hidden" name="action" value="update" />
    <p>
        <b>General</b><br/>
        <input type="text" name="username" id="username" maxlength="30" placeholder="<?php printf('%s', htmlspecialchars($_SESSION['login_user'])); ?>" class="register_field_standard"  readonly="readonly" />
        <input type="password" name="password" id="password" maxlength="20" placeholder="Password" class="register_field_standard" />
        <input type="password" name="password2" id="password2" placeholder="Repeat" class="register_field_standard" />
        <input type="text" name="email" id="email" maxlength="128" placeholder="E-Mail" class="register_field_standard" <?php if (isset($email)) { printf('value="%s"', htmlspecialchars($email)); } ?>/></p>
    </p>
    <p>
        <p><b>Additional</b><br/>
        <input type="text" name="firstname" id="firstname" maxlength="20" placeholder="First name" class="register_field_standard" <?php if (isset($firstname)) { printf('value="%s"', htmlspecialchars($firstname)); } ?>/>
        <input type="text" name="lastname" id="lastname" maxlength="20" placeholder="Last name" class="register_field_standard" <?php if (isset($lastname)) { printf('value="%s"', htmlspecialchars($lastname)); } ?>/>
        <input type="text" name="location" id="location" maxlength="20" placeholder="Location" class="register_field_standard" <?php if (isset($location)) { printf('value="%s"', htmlspecialchars($location)); } ?>/>
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
<script type="text/javascript" src="../lib/jquery.min.js"></script>
<?php
js_sanitize_password();
js_sanitize_email();
js_sanitize_string();
?>
<script type="text/javascript">
$(document).ready(function() {
    $('input#password').focus();

    $('form').submit(function(event) {
        return sanitize_password('input#password', 'input#password2')
            && sanitize_email('input#email')
            && sanitize_string('input#firstname', false, 'Firstname')
            && sanitize_string('input#lastname', false, 'Lastname')
            && sanitize_string('input#location', false, 'Location');
    });
});
</script>

<?php
include("footer.php");
?>
