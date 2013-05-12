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
function total_coffees_for_profile($profileid) {
    global $dbconn;
    $sql = sprintf(
        "SELECT COUNT(cid) AS total FROM cs_coffees WHERE cuid = %d",
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    $total = 0;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $total = $row['total'];
    }
    $result->close();
    return $total;
}

/**
 * Return total mate for user profile.
 */
function total_mate_for_profile($profileid) {
    global $dbconn;
    $sql = sprintf(
        "SELECT COUNT(mid) AS total FROM cs_mate WHERE cuid = %d",
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    $total = 0;
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $total = $row['total'];
    }
    $result->close();
    return $total;
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
        "SELECT 0 AS label, COUNT(cid) AS value, DATE_FORMAT(cdate, '%%H') AS hour
         FROM cs_coffees
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP, '%%Y-%%m-%%d') = DATE_FORMAT(cdate, '%%Y-%%m-%%d')
           AND cuid = %d
         GROUP BY hour
         UNION
         SELECT 1 AS label, COUNT(mid) AS value, DATE_FORMAT(mdate, '%%H') AS hour
         FROM cs_mate
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP, '%%Y-%%m-%%d') = DATE_FORMAT(mdate, '%%Y-%%m-%%d')
           AND cuid = %d
         GROUP BY hour",
        $profileid,
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['hour'])][$row['label']] = $row['value'];
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
        "SELECT 0 AS label, COUNT(cid) AS value, DATE_FORMAT(cdate, '%%d') AS day
         FROM cs_coffees
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y-%%m') = DATE_FORMAT(cdate, '%%Y-%%m')
           AND cuid = %d
         GROUP BY day
         UNION
         SELECT 1 AS label, COUNT(mid) AS value, DATE_FORMAT(mdate, '%%d') AS day
         FROM cs_mate
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%%Y-%%m') = DATE_FORMAT(mdate, '%%Y-%%m')
           AND cuid = %d
         GROUP BY day",
        $profileid,
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['day'])][$row['label']] = $row['value'];
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
        "SELECT 0 AS label, COUNT(cid) AS value, DATE_FORMAT(cdate,'%%m') AS month
         FROM cs_coffees
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%%Y') = DATE_FORMAT(cdate, '%%Y')
           AND cuid = %d
         GROUP BY month
         UNION
         SELECT 1 AS label, COUNT(mid) AS value, DATE_FORMAT(mdate, '%%m') AS month
         FROM cs_mate
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(),'%%Y') = DATE_FORMAT(mdate, '%%Y')
           AND cuid = %d
         GROUP BY month",
        $profileid,
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['month'])][$row['label']] = $row['value'];
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
        "SELECT 0 AS label, COUNT(cid) AS value, DATE_FORMAT(cdate, '%%H') AS hour
         FROM cs_coffees
         WHERE cuid = %d
         GROUP BY hour
         UNION
         SELECT 1 AS label, COUNT(mid) AS value, DATE_FORMAT(mdate, '%%H') AS hour
         FROM cs_mate
         WHERE cuid = %d
         GROUP BY hour",
        $profileid,
        $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[intval($row['hour'])][$row['label']] = $row['value'];
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
        "SELECT 0 AS label, COUNT(cid) AS value, DATE_FORMAT(cdate, '%%a') AS wday
         FROM cs_coffees
         WHERE cuid = %d
         GROUP BY wday
         UNION
         SELECT 1 AS label, COUNT(mid) AS value, DATE_FORMAT(mdate, '%%a') AS wday
         FROM cs_mate
         WHERE cuid = %d
         GROUP BY wday",
        $profileid,
        $profileid);
    if (($result=$dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
    }
    while ($row=$result->fetch_array(MYSQLI_ASSOC)) {
        $retval[$row['wday']][$row['label']] = $row['value'];
    }
    $result->close();
    return $retval;
}
?>
