<?php
include("auth/lock.php");
include_once('includes/common.php');
include_once('includes/queries.php');

/**
 * Export a user's coffee and mate history.
 */
function export_csv($uid) {
    $files = array();

    $iterate = array(
        array('coffee', 0),
        array('mate', 1));

    foreach ($iterate as $current) {
        $file = tempnam(sys_get_temp_dir(), 'coffeestats');
        $nowstr = date('Y-m-d H:i');
        $filename = sprintf("%s-%s.csv", $current[0], $nowstr);
        $filepart = array(
            'content-type' => 'text/csv; charset=utf-8',
            'description' => sprintf('Your coffee history until %s', $nowstr),
            'realfile' => $file,
            'filename' => $filename);

        $caffeinerows = find_caffeine_by_uid_and_type($uid, $current[1]);
        if (($csvfile = fopen($file, 'w')) !== FALSE) {
            fputcsv($csvfile, array("Timestamp"));
            foreach ($caffeinerows as $row) {
                fputcsv($csvfile, array($row['thedate']));
            }
            fclose($csvfile);
        }
        else {
            flash("Problem during export", FLASH_ERROR);
            return NULL;
        }
        array_push($files, $filepart);
    }
    return $files;
}

/**
 * Update a user's profile from submitted form data.
 */
function update_user($uuserid, &$profile) {
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
    if ($email !== FALSE) {
        if (($email = unique_email($email, $uuserid)) === FALSE) {
            flash(
                "The given email address is already in use!", FLASH_ERROR);
        }
    }
    $password = sanitize_password($_POST['password'], $_POST['password2'], TRUE);
    $firstname = sanitize_string($_POST['firstname'], FALSE);
    $lastname = sanitize_string($_POST['lastname'], FALSE);
    $location = sanitize_string($_POST['location'], FALSE);

    if (($email === FALSE) || ($password === FALSE) || ($firstname === FALSE) ||
        ($lastname === FALSE) || ($location === FALSE))
    {
        return;
    }

    if (!empty($password)) {
        set_user_password($uuserid, $password);
        flash("Successfully changed your password!", FLASH_SUCCESS);
    }

    if (($profile['firstname'] != $firstname) ||
        ($profile['lastname'] != $lastname) ||
        ($profile['location'] != $location))
    {
        set_user_information($uuserid, $firstname, $lastname, $location);
        flash(
            "Successfully updated your profile information!", FLASH_SUCCESS);
    }
    if ($profile['email'] != $email) {
        send_change_email_link($email, $uuserid);
        flash(
            "We sent an email with a link that you need to open to " .
            "confirm the change of your email address.", FLASH_INFO);
    }
}


$profile = load_user_profile($_SESSION['login_id']);
if ($profile === NULL) {
    errorpage('404 Not Found', 'No such user', '404 Not Found');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Switching between modes
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
        case 'export':
            $caffeinefiles = &export_csv($_SESSION['login_id']);
            $sendermail = get_setting(MAIL_FROM_ADDRESS);
            send_caffeine_mail($profile['email'], $caffeinefiles);
            // delete temporary files
            foreach ($caffeinefiles as $cfile) {
                unlink($cfile['realfile']);
            }
            flash(
                'Your data has been exported. You will receive an email ' .
                'with two CSV files with your coffee and mate ' .
                'registrations attached.',
                FLASH_INFO);
            redirect_to($_SERVER['REQUEST_URI']);
            break;
        case 'update':
            update_user($_SESSION['login_id'], $profile);
            redirect_to($_SERVER['REQUEST_URI']);
            break;
        case 'delete':
            // TODO: make deletion a user self service
            send_user_deletion($_SESSION['login_user'],$_SESSION['login_id']);
            flash(
                sprintf(
                    'We received your deletion request. Your account will ' .
                    'be deleted with all its information within the next ' .
                    'week. If you change your mind, feel free to mail us at %s',
                    get_setting(SITE_ADMINMAIL)),
                FLASH_INFO);
            redirect_to($_SERVER['REQUEST_URI']);
            break;
        default:
            error_log(sprintf('Invalid call wrong POST action %s', 
                $_POST['action']));
            errorpage('Bad request', 'Your request contained an invalid action', 
                '400 Bad Request');
        }
    }
    redirect_to('index');
}

include('includes/jsvalidation.php');

// TODO: add timezone selection (see https://bugs.n0q.org/view.php?id=19#c114)
// TODO: add public flag (see https://bugs.n0q.org/view.php?id=7)
include("header.php");
?>
<div class="white-box">
    <h2>Update your profile</h2>
    <p>You may change your information.</p>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="coffeeform">
    <div class="leftform">
        <h3>General</h3>
        <input type="text" name="username" id="username" maxlength="30" value="<?php echo htmlspecialchars($profile['login']); ?>" readonly="readonly" />
        <input type="password" name="password" pattern=".{8,}" id="password" maxlength="20" placeholder="Password" autofocus />
        <input type="password" name="password2" pattern=".{8,}" id="password2" placeholder="Repeat" />
    </div>
    <div class="leftform">
        <h3>Additional</h3>
        <input type="text" name="firstname" id="firstname" maxlength="20" placeholder="First name" <?php if (!empty($profile['firstname'])) { printf('value="%s"', htmlspecialchars($profile['firstname'])); } ?>/>
        <input type="text" name="lastname" id="lastname" maxlength="20" placeholder="Last name" <?php if (!empty($profile['lastname'])) { printf('value="%s"', htmlspecialchars($profile['lastname'])); } ?>/>
        <input type="text" name="location" id="location" maxlength="20" placeholder="Location" <?php if (!empty($profile['location'])) { printf('value="%s"', htmlspecialchars($profile['location'])); } ?>/>
    </div>
    <input type="email" name="email" pattern="[A-Za-z0-9._%+-]+@[^@]+" required id="email" maxlength="128" placeholder="E-Mail" value="<?php echo htmlspecialchars($profile['email']); ?>" />
    <div class="clearfix"></div>
    <input type="hidden" name="action" value="update" />
    <p><input type="submit" name="submit" value="Update my settings" /></p>
    </form>
    <div class="clear"></div>
</div>

<div class="white-box">
    <h2>Export your Activity</h2>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="coffeeform">
    <input type="hidden" name="action" value="export" />
    <p>Your data is yours. You will recieve the csv files via mail.</p>
    <p><input type="submit" name="submit" value="Export, please!" /></p>
    </form>
</div>
<div><!-- space --></div>
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
    $('input#password').bind('invalid', pwfieldvalidation);
    $('input#password2').bind('invalid', pwfieldvalidation);
    $('input#email').bind('invalid', emailfieldvalidation);

    if (!("autofocus" in document.createElement('input'))) {
        $("input#password").focus();
    }

    $('form').submit(function(event) {
        return sanitize_password('input#password', 'input#password2', true)
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
