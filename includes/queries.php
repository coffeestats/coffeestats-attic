<?php
if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    header('Status: 301 Moved Permanently');
    header('Location: ../index');
    exit();
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
        handle_mysql_error();
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
        handle_mysql_error();
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
        handle_mysql_error();
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
    $sql=sprintf(
        "SELECT ctype, COUNT(cid) AS value, DATE_FORMAT(cdate, '%%a') AS wday
         FROM cs_caffeine
         WHERE cuid = %d
         GROUP BY wday, ctype",
        $profileid);
    if (($result=$dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
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
 *
 * TODO: handle mate too
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
 *
 * TODO: handle mate too
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
 * Returns the most recently joined users.
 */
function recently_joined_users($count) {
    global $dbconn;
    $sql = sprintf(
        "SELECT ulogin FROM cs_users ORDER BY ujoined DESC LIMIT %d",
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
?>
