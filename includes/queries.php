<?php
if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    header('Status: 301 Moved Permanently');
    header('Location: ../index');
    exit();
}

include_once(sprintf('%s/common.php', dirname(__FILE__)));

/**
 * Handle a MySQL error, log to error log and show an error page to the user.
 */
function handle_mysql_error($sql=NULL) {
    global $dbconn;
    if ($dbconn->errno !== 0) {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        error_log(sprintf(
            "%s line %d: MySQL error %d: %s%s",
            $backtrace[0]['file'], $backtrace[0]['line'],
            $dbconn->errno, $dbconn->error, ($sql === NULL) ? "" : "\n" . $sql));
        errorpage("Error", "Sorry, we have a problem.", "500 Internal Server Error");
    }
}

/*
 * Bundle common queries.
 */

/**
 * Return total coffees for user profile.
 */
function total_caffeine_for_profile($profileid) {
    global $dbconn;
    $retval = array('coffees' => 0, 'mate' => 0);
    $sql = sprintf(
        "SELECT ctype, COUNT(cid) AS value
         FROM cs_caffeine WHERE cuid = %d
         GROUP BY ctype",
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        switch ($row['ctype']) {
        case 0:
            $retval['coffees'] = $row['value'];
            break;
        case 1:
            $retval['mate'] = $row['value'];
            break;
        default:
            error_log(sprintf("Unexpected caffeine type %d", $row['ctype']));
            errorpage("Unexpected error", "Unknown caffeine type");
        }
    }
    $result->close();
    return $retval;
}

/**
 * Return total coffees and mate.
 */
function total_caffeine() {
    global $dbconn;
    $retval = array('coffees' => 0, 'mate' => 0);
    $sql =
        "SELECT ctype, COUNT(cid) AS value
         FROM cs_caffeine GROUP BY ctype";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        switch ($row['ctype']) {
        case 0:
            $retval['coffees'] = $row['value'];
            break;
        case 1:
            $retval['mate'] = $row['value'];
            break;
        default:
            error_log(sprintf("Unexpected caffeine type %d", $row['ctype']));
            errorpage("Unexpected error", "Unknown caffeine type");
        }
    }
    $result->close();
    return $retval;
}

/**
 * Return series of hourly coffees and mate on current day for user profile.
 */
function hourly_caffeine_for_profile($profileid) {
    global $dbconn;
    $retval = array();
    for ($counter = 0; $counter <= 23; $counter++) {
        $retval[$counter] = array(0, 0);
    }
    $sql = sprintf(
        "SELECT ctype, COUNT(cid) AS value, DATE_FORMAT(cdate, '%%H') AS hour
         FROM cs_caffeine
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP, '%%Y-%%m-%%d') = DATE_FORMAT(cdate, '%%Y-%%m-%%d')
           AND cuid = %d
         GROUP BY hour, ctype",
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['hour'])][$row['ctype']] = $row['value'];
    }
    $result->close();
    return $retval;
}

/**
 * Return series of hourly coffees and mate.
 */
function hourly_caffeine_overall() {
    global $dbconn;
    $retval = array();
    for ($counter = 0; $counter <= 23; $counter++) {
        $retval[$counter] = array(0, 0);
    }
    $sql = "SELECT ctype, COUNT(cid) AS value, DATE_FORMAT(cdate, '%H') AS hour
            FROM cs_caffeine
            WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d') = DATE_FORMAT(cdate, '%Y-%m-%d')
            GROUP BY hour, ctype";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['hour'])][$row['ctype']] = $row['value'];
    }
    $result->close();
    return $retval;
}

/**
 * Return series of daily coffees and mate in current month for user profile.
 */
function daily_caffeine_for_profile($profileid) {
    global $dbconn;
    $now = getdate();
    $maxdays = cal_days_in_month(CAL_GREGORIAN, $now['mon'], $now['year']);
    $retval = array();
    for ($counter = 1; $counter <= $maxdays; $counter++) {
        $retval[$counter] = array(0, 0);
    }
    $sql = sprintf(
        "SELECT ctype, COUNT(cid) AS value, DATE_FORMAT(cdate, '%%d') AS day
         FROM cs_caffeine
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y-%%m') = DATE_FORMAT(cdate, '%%Y-%%m')
           AND cuid = %d
         GROUP BY day, ctype",
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['day'])][$row['ctype']] = $row['value'];
    }
    $result->close();
    return $retval;
}

/**
 * Return a series of daily coffees and mate in current month.
 */
function daily_caffeine_overall() {
    global $dbconn;
    $retval = array();
    $now = getdate();
    $maxdays = cal_days_in_month(CAL_GREGORIAN, $now['mon'], $now['year']);
    for ($counter = 1; $counter <= $maxdays; $counter++) {
        $retval[$counter] = array(0, 0);
    }
    $sql = "SELECT ctype, COUNT(cid) AS value, DATE_FORMAT(cdate, '%d') AS day
            FROM cs_caffeine
            WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m') = DATE_FORMAT(cdate, '%Y-%m')
            GROUP BY day, ctype";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['day'])][$row['ctype']] = $row['value'];
    }
    $result->close();
    return $retval;
}

/**
 * Return a series of monthly coffees and mate in current month for user profile.
 */
function monthly_caffeine_for_profile($profileid) {
    global $dbconn;
    $retval = array();
    for ($counter = 1; $counter <= 12; $counter++) {
        $retval[$counter] = array(0, 0);
    }
    $sql = sprintf(
        "SELECT ctype, COUNT(cid) AS value, DATE_FORMAT(cdate,'%%m') AS month
         FROM cs_caffeine
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%%Y') = DATE_FORMAT(cdate, '%%Y')
           AND cuid = %d
         GROUP BY month, ctype",
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['month'])][$row['ctype']] = $row['value'];
    }
    $result->close();
    return $retval;
}

/**
 * Return a series of monthly coffees and mate in current month.
 */
function monthly_caffeine_overall() {
    global $dbconn;
    $retval = array();
    for ($counter = 1; $counter <= 12; $counter++) {
        $retval[$counter] = array(0, 0);
    }
    $sql =
        "SELECT ctype, COUNT(cid) AS value, DATE_FORMAT(cdate,'%m') AS month
         FROM cs_caffeine
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y') = DATE_FORMAT(cdate, '%Y')
         GROUP BY month, ctype";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['month'])][$row['ctype']] = $row['value'];
    }
    $result->close();
    return $retval;
}

/**
 * Return a series of hourly coffees and mate for the whole timespan of a
 * user's membership.
 */
function hourly_caffeine_for_profile_overall($profileid) {
    global $dbconn;
    $retval = array();
    for ($counter = 0; $counter <= 23; $counter++) {
        $retval[$counter] = array(0, 0);
    }
    $sql = sprintf(
        "SELECT ctype, COUNT(cid) AS value, DATE_FORMAT(cdate, '%%H') AS hour
         FROM cs_caffeine
         WHERE cuid = %d
         GROUP BY hour, ctype",
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['hour'])][$row['ctype']] = $row['value'];
    }
    $result->close();
    return $retval;
}

/**
 * Return a series of coffees and mate for all time.
 */
function hourly_caffeine_alltime() {
    global $dbconn;
    $retval = array();
    for ($counter = 0; $counter <= 23; $counter++) {
        $retval[$counter] = array(0, 0);
    }
    $sql =
        "SELECT ctype, COUNT(cid) AS value, DATE_FORMAT(cdate, '%H') AS hour
         FROM cs_caffeine
         GROUP BY hour, ctype";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['hour'])][$row['ctype']] = $row['value'];
    }
    $result->close();
    return $retval;
}

/**
 * Return a series of coffees and mate per weekday for the whole timestamp of a
 * user's membership.
 */
function weekdaily_caffeine_for_profile_overall($profileid) {
    global $dbconn;
    $retval = array();
    $weekdays = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
    for ($counter = 0; $counter < count($weekdays); $counter++) {
        $retval[$weekdays[$counter]] = array(0, 0);
    }
    $sql = sprintf(
        "SELECT ctype, COUNT(cid) AS value, DATE_FORMAT(cdate, '%%a') AS wday
         FROM cs_caffeine
         WHERE cuid = %d
         GROUP BY wday, ctype",
        $profileid);
    if (($result=$dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row=$result->fetch_array(MYSQLI_ASSOC)) {
        $retval[$row['wday']][$row['ctype']] = $row['value'];
    }
    $result->close();
    return $retval;
}

/**
 * Return a series of coffees and mate per weekday for all time.
 */
function weekdaily_caffeine_alltime() {
    global $dbconn;
    $retval = array();
    $weekdays = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
    for ($counter = 0; $counter < count($weekdays); $counter++) {
        $retval[$weekdays[$counter]] = array(0, 0);
    }
    $sql =
        "SELECT ctype, COUNT(cid) AS value, DATE_FORMAT(cdate, '%a') AS wday
         FROM cs_caffeine
         GROUP BY wday, ctype";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[$row['wday']][$row['ctype']] = $row['value'];
    }
    $result->close();
    return $retval;
}

/**
 * Returns a set of random users.
 */
function random_users($count) {
    global $dbconn;
    $sql = sprintf(
        "SELECT ulogin, ufname, uname, ulocation,
         (SELECT COUNT(cid) FROM cs_caffeine WHERE cuid=uid AND ctype=0) AS coffees,
         (SELECT COUNT(cid) FROM cs_caffeine WHERE cuid=uid AND ctype=1) AS mate
         FROM cs_users ORDER BY RAND() LIMIT %d",
        $count);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = array();
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        array_push($retval, $row);
    }
    $result->close();

    return $retval;
}

/**
 * Returns the latest caffeine activity.
 */
function latest_caffeine_activity($count) {
    global $dbconn;
    $sql = sprintf(
        "SELECT cid, ctype, ulogin, cdate, ctimezone
         FROM cs_caffeine JOIN cs_users ON cuid=uid
         ORDER BY cdate DESC LIMIT %d",
        $count);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = array();
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        array_push($retval, $row);
    }
    $result->close();
    return $retval;
}

/**
 * Returns the top caffeine consumers.
 */
function top_caffeine_consumers_total($count, $ctype=0) {
    global $dbconn;
    $sql = sprintf(
        "SELECT COUNT(cid) AS total, ulogin
         FROM cs_caffeine JOIN cs_users ON cuid=uid
         WHERE ctype=%d
         GROUP BY ulogin
         ORDER BY COUNT(cid) DESC LIMIT %d",
        $ctype, $count);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = array();
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        array_push($retval, $row);
    }
    $result->close();
    return $retval;
}

/**
 * Returns the top average caffeine consumers.
 */
function top_caffeine_consumers_average($count, $ctype=0) {
    global $dbconn;
    $sql = sprintf(
        "SELECT ulogin, COUNT(cid) / (DATEDIFF(CURRENT_DATE, MIN(cdate)) + 1) AS average
         FROM cs_caffeine JOIN cs_users ON cuid=uid
         WHERE ctype=%d
         GROUP BY ulogin
         ORDER BY average DESC LIMIT %d",
        $ctype, $count);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = array();
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        array_push($retval, $row);
    }
    $result->close();
    return $retval;
}

/**
 * Return latest $count users ordered by ujoined in the given direction.
 */
function _fetch_users_by_jointime($count, $direction) {
    global $dbconn;
    $sql = sprintf(
        "SELECT ulogin, DATEDIFF(CURRENT_TIMESTAMP, ujoined) AS days
         FROM cs_users ORDER BY ujoined %s LIMIT %d",
        $direction, $count);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = array();
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        array_push($retval, $row);
    }
    $result->close();
    return $retval;
}

/**
 * Returns the most recently joined users.
 */
function recently_joined_users($count) {
    return _fetch_users_by_jointime($count, 'DESC');
}

/**
 * Returns the earliest users.
 */
function longest_joined_users($count) {
    return _fetch_users_by_jointime($count, 'ASC');
}

/**
 * Return the latest entries for the given user.
 */
function latest_entries($profileid, $count=10) {
    global $dbconn;
    $sql = sprintf(
        "SELECT cid, cdate, ctype, ctimezone FROM cs_caffeine WHERE cuid=%d ORDER BY centrytime DESC LIMIT %d",
        $profileid, $count);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = array();
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        array_push($retval, $row);
    }
    $result->close();
    return $retval;
}

/**
 * Fetch the entry with the given id if it matches the given user.
 */
function fetch_entry($cid, $profileid) {
    global $dbconn;
    $sql = sprintf(
        "SELECT cid, ctype, cdate, ctimezone FROM cs_caffeine
         WHERE cid=%d AND cuid=%d",
        $cid, $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = NULL;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval = $row;
    }
    return $retval;
}

/**
 * Delete the given entry if it matches the given user.
 */
function delete_caffeine_entry($cid, $profileid) {
    global $dbconn;
    $sql = sprintf(
        "DELETE FROM cs_caffeine WHERE cid=%d AND cuid=%d",
        $cid, $profileid);
    if (($result = $dbconn->query($sql)) === FALSE) {
        handle_mysql_error($sql);
    }
    return (($dbconn->affected_rows) === 1);
}

/**
 * Set the user's time zone information.
 */
function set_user_timezone($profileid, $tzname) {
    global $dbconn;
    $sql = sprintf(
        "UPDATE cs_users SET utimezone='%s' WHERE uid=%d",
        $dbconn->real_escape_string($tzname),
        $profileid);
    if (($result = $dbconn->query($sql)) === FALSE) {
        handle_mysql_error($sql);
    }
    $success = (($dbconn->affected_rows) === 1);
    // set existing entries without timezone to the selected timezone
    $sql = sprintf(
        "UPDATE cs_caffeine SET ctimezone='%s'
         WHERE cuid=%d AND ctimezone IS NULL",
        $dbconn->real_escape_string($tzname),
        $profileid);
    if (($result = $dbconn->query($sql)) === FALSE) {
        handle_mysql_error($sql);
    }
    return $success;
}

/**
 * Check whether the given email address is unique.
 */
function unique_email($email, $uid) {
    global $dbconn;
    $sql = sprintf(
        "SELECT uid FROM cs_users WHERE uemail='%s' AND uid <> %d",
        $dbconn->real_escape_string($email), $uid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        return FALSE;
    }
    return $email;
}

/**
 * Find the data for the given action code.
 */
function find_action_data($actioncode) {
    global $dbconn;
    $sql = sprintf(
        "SELECT cuid, atype, adata FROM cs_actions WHERE acode='%s'",
        $dbconn->real_escape_string($actioncode));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = NULL;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval = $row;
    }
    $result->close();
    return $retval;
}

/**
 * Create an entry in the cs_actions table.
 */
function create_action($uid, $action_type, $actioncode, $data) {
    global $dbconn;
    $sql = sprintf(
        "INSERT INTO cs_actions
         (cuid, acode,
          created, validuntil,
          atype, adata)
         VALUES
         (%d, '%s',
          CURRENT_TIMESTAMP, CURRENT_TIMESTAMP + INTERVAL 24 HOUR,
          %d, '%s')",
        $uid, $dbconn->real_escape_string($actioncode),
        $action_type, $dbconn->real_escape_string($data));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
}

/**
 * Delete the action with the given action code.
 */
function delete_action($actioncode) {
    global $dbconn;
    $sql = sprintf(
        "DELETE FROM cs_actions WHERE acode='%s'",
        $dbconn->real_escape_string($actioncode));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
}

/**
 * Set the user with the given user id to active.
 */
function set_user_active($uid) {
    global $dbconn;
    $sql = sprintf("UPDATE cs_users SET uactive=1 WHERE uid=%d", $uid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    return (($dbconn->affected_rows) === 1);
}

/**
 * Set the email address of the given user.
 */
function set_user_email($uid, $email) {
    global $dbconn;
    $sql = sprintf(
        "UPDATE cs_users SET uemail='%s' WHERE uid=%d",
        $dbconn->real_escape_string($email), $uid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    return (($dbconn->affected_rows) === 1);
}

/**
 * Find the uid of the user with the given login.
 */
function find_user_uid_by_login($login) {
    global $dbconn;
    $sql = sprintf(
        "SELECT uid FROM cs_users WHERE ulogin='%s'",
        $dbconn->real_escape_string($login));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $uid = NULL;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $uid = $row['uid'];
    }
    $result->close();
    return $uid;
}

/**
 * Set the given user's password hash value.
 */
function set_user_password($uid, $password) {
    global $dbconn;
    $sql = sprintf(
        "UPDATE cs_users SET ucryptsum='%s' WHERE uid=%d",
        hash_password($password), $uid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    return (($dbconn->affected_rows) === 1);
}

/**
 * Existance check for a user with the given login. This function returns the
 * user login if a matching row exists.
 */
function get_login_for_user_with_login($login) {
    global $dbconn;
    $sql = sprintf(
        "SELECT ulogin FROM cs_users WHERE ulogin='%s'",
        $dbconn->real_escape_string($login));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = NULL;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval = $row['ulogin'];
    }
    $result->close();
    return $retval;
}

/**
 * Find the user information required for login (uid, password hash and
 * timezone). for the give user name.
 */
function find_user_information_for_login($login) {
    global $dbconn;
    $sql = sprintf(
        "SELECT uid, ucryptsum, utimezone FROM cs_users
         WHERE ulogin='%s' AND uactive=1",
        $dbconn->real_escape_string($login));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = NULL;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval = $row;
    }
    $result->close();
    return $retval;
}

/**
 * Check whether a user with either the given login or the given email address
 * exists.
 */
function find_user_exist_for_login_or_email($login, $email) {
    global $dbconn;
    $sql = sprintf(
        "SELECT uid FROM cs_users WHERE ulogin='%s' OR uemail='%s'",
        $dbconn->real_escape_string($login),
        $dbconn->real_escape_string($email));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = FALSE;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval = TRUE;
    }
    $result->close();
    return $retval;
}

/**
 * Create a user with the given data.
 */
function create_user(
    $login, $email, $firstname, $lastname, $passwordhash, $location,
    $otrtoken)
{
    global $dbconn;
    $sql = sprintf(
        "INSERT INTO cs_users (
            ulogin, uemail, ufname, uname, ucryptsum, ujoined,
            ulocation, upublic, utoken, uactive)
         VALUES (
            '%s', '%s', '%s', '%s', '%s', NOW(),
            '%s', 1, '%s', 0)",
        $dbconn->real_escape_string($login),
        $dbconn->real_escape_string($email),
        $dbconn->real_escape_string($firstname),
        $dbconn->real_escape_string($lastname),
        $dbconn->real_escape_string($passwordhash),
        $dbconn->real_escape_string($location),
        $dbconn->real_escape_string($otrtoken));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
}

/**
 * Find user information (firstname, login and uid) for a given email address.
 */
function find_user_firstname_login_uid_by_email($email) {
    global $dbconn;
    $sql = sprintf(
        "SELECT ufname, ulogin, uid FROM cs_users WHERE uemail='%s'",
        $dbconn->real_escape_string($email));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = NULL;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval = $row;
    }
    $result->close();
    return $retval;
}

/**
 * Find user information (firstname, login, uid, email) for a given uid.
 */
function find_user_firstname_login_uid_email_by_uid($uid) {
    global $dbconn;
    $sql = sprintf(
        "SELECT ufname, ulogin, uid, uemail FROM cs_users WHERE uid=%d",
        $uid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = NULL;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval = $row;
    }
    $result->close();
    return $retval;
}

/**
 * Performs a cleanup of the action table.
 */
function clean_expired_actions() {
    global $dbconn;
    $sql = "DELETE FROM cs_actions WHERE validuntil < CURRENT_TIMESTAMP";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
}

/**
 * Perform a cleanup of inactive users that have no coffee or mate registered
 * yet.
 */
function clean_inactive_users() {
    global $dbconn;
    $sql = "DELETE FROM cs_users
        WHERE uactive=0 AND NOT EXISTS (
          SELECT cid FROM cs_caffeine WHERE cuid=uid)
        AND ujoined < (CURRENT_TIMESTAMP - INTERVAL 30 DAY)";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
}

/**
 * Find caffeine dose with the given type for the given user in a five minute
 * interval around the given point of time.
 */
function find_recent_caffeine($regtime, $uid, $ctype) {
    global $dbconn;
    $sql = sprintf(
        'SELECT cid, cdate, ctimezone
         FROM cs_caffeine
         WHERE ctype = %3$d
           AND cdate > (\'%1$s\' - INTERVAL 5 MINUTE)
           AND cdate < (\'%1$s\' + INTERVAL 5 MINUTE)
           AND cuid = %2$d',
        $dbconn->real_escape_string($regtime), $uid, $ctype);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = NULL;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval = $row;
    }
    $result->close();
    return $retval;
}

/**
 * Create an entry in the cs_caffeine table.
 */
function create_caffeine($regtime, $uid, $ctype) {
    global $dbconn;
    $sql = sprintf(
        "INSERT INTO cs_caffeine (cuid, ctype, cdate, centrytime, ctimezone)
         SELECT uid, %d, '%s', UTC_TIMESTAMP, utimezone
         FROM cs_users WHERE uid=%d",
        $ctype, $dbconn->real_escape_string($regtime), $uid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
}

/**
 * Find information about the user with the given uid.
 */
function find_user_by_uid($uid) {
    global $dbconn;
    $sql = sprintf(
        "SELECT ulogin, ufname, uname, ulocation, uemail, utimezone
         FROM cs_users WHERE uid=%d",
        $uid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = NULL;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval = $row;
    }
    $result->close();
    return $retval;
}

/**
 * Find information about the user (uid, token, login, timezone) identified by
 * the given login and token.
 */
function find_user_uid_token_login_and_timezone_by_login_and_token(
    $login, $token)
{
    global $dbconn;
    $sql = sprintf(
        "SELECT uid, utoken, ulogin, utimezone FROM cs_users
         WHERE ulogin='%s' AND utoken='%s'",
        $dbconn->real_escape_string($login),
        $dbconn->real_escape_string($token));
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    $retval = NULL;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval = $row;
    }
    $result->close();
    return $retval;
}
?>
