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
        "SELECT 'coffees' AS label, COUNT(cid) AS value FROM cs_coffees WHERE cuid = %d
         UNION
         SELECT 'mate' AS label, COUNT(mid) AS value FROM cs_mate WHERE cuid = %d",
        $profileid, $profileid);
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[$row['label']] = $row['value'];
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
        "SELECT 'coffees' AS label, COUNT(cid) AS value FROM cs_coffees
         UNION
         SELECT 'mate' AS label, COUNT(mid) AS value FROM cs_mate";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[$row['label']] = $row['value'];
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
 * Return series of hourly coffees and mate.
 */
function hourly_caffeine_overall() {
    global $dbconn;
    $retval = array();
    for ($counter = 0; $counter <= 23; $counter++) {
        $retval[$counter] = array(0, 0);
    }
    $sql = "SELECT 0 AS label, COUNT(cid) AS value, DATE_FORMAT(cdate, '%H') AS hour
            FROM cs_coffees
            WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d') = DATE_FORMAT(cdate, '%Y-%m-%d')
            GROUP BY hour
            UNION
            SELECT 1 AS label, COUNT(mid) AS value, DATE_FORMAT(mdate, '%H') AS hour
            FROM cs_mate
            WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d') = DATE_FORMAT(mdate, '%Y-%m-%d')
            GROUP BY hour";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
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
    $sql = "SELECT 0 AS label, COUNT(cid) AS value, DATE_FORMAT(cdate, '%d') AS day
            FROM cs_coffees
            WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m') = DATE_FORMAT(cdate, '%Y-%m')
            GROUP BY day
            UNION
            SELECT 1 AS label, COUNT(mid) AS value, DATE_FORMAT(mdate, '%d') AS day
            FROM cs_mate
            WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m') = DATE_FORMAT(mdate, '%Y-%m')
            GROUP BY day";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
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
 * Return a series of monthly coffees and mate in current month.
 */
function monthly_caffeine_overall() {
    global $dbconn;
    $retval = array();
    for ($counter = 1; $counter <= 12; $counter++) {
        $retval[$counter] = array(0, 0);
    }
    $sql =
        "SELECT 0 AS label, COUNT(cid) AS value, DATE_FORMAT(cdate,'%m') AS month
         FROM cs_coffees
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y') = DATE_FORMAT(cdate, '%Y')
         GROUP BY month
         UNION
         SELECT 1 AS label, COUNT(mid) AS value, DATE_FORMAT(mdate, '%m') AS month
         FROM cs_mate
         WHERE DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y') = DATE_FORMAT(mdate, '%Y')
         GROUP BY month";
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
 * Return a series of coffees and mate for all time.
 */
function hourly_caffeine_alltime() {
    global $dbconn;
    $retval = array();
    for ($counter = 0; $counter <= 23; $counter++) {
        $retval[$counter] = array(0, 0);
    }
    $sql =
        "SELECT 0 AS label, COUNT(cid) AS value, DATE_FORMAT(cdate, '%H') AS hour
         FROM cs_coffees
         GROUP BY hour
         UNION
         SELECT 1 AS label, COUNT(mid) AS value, DATE_FORMAT(mdate, '%H') AS hour
         FROM cs_mate
         GROUP BY hour";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error();
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
        "SELECT 0 AS label, COUNT(cid) AS value, DATE_FORMAT(cdate, '%a') AS wday
         FROM cs_coffees
         GROUP BY wday
         UNION
         SELECT 1 AS label, COUNT(mid) AS value, DATE_FORMAT(mdate, '%a') AS wday
         FROM cs_mate
         GROUP BY wday";
    if (($result = $dbconn->query($sql, MYSQLI_USE_RESULT)) === FALSE) {
        handle_mysql_error($sql);
    }
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $retval[$row['wday']][$row['label']] = $row['value'];
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
         (SELECT COUNT(cid) FROM cs_coffees WHERE cuid=uid) AS coffees,
         (SELECT COUNT(mid) FROM cs_mate WHERE cuid=uid) AS mate
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
        "SELECT label, ulogin, date
         FROM (
             SELECT 0 AS label, ulogin, cdate AS date
             FROM cs_coffees JOIN cs_users ON cuid=uid
             UNION
             SELECT 1 AS label, ulogin, mdate AS date
             FROM cs_mate JOIN cs_users ON cuid=uid
         ) AS x
         ORDER BY date DESC LIMIT %d",
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
function top_caffeine_consumers_total($count) {
    global $dbconn;
    $sql = sprintf(
        "SELECT COUNT(cid) AS total, cs_users.ulogin AS ulogin
         FROM cs_coffees,cs_users
         WHERE cs_coffees.cuid = cs_users.uid
         GROUP BY cs_users.ulogin ORDER BY COUNT(cid) DESC LIMIT %d",
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
 * Returns the top average caffeine consumers.
 *
 * TODO: handle mate too
 */
function top_caffeine_consumers_average($count) {
    global $dbconn;
    $sql = sprintf(
        "SELECT ulogin, COUNT(cid) / (DATEDIFF(CURRENT_DATE, MIN(cdate)) + 1) AS average
         FROM cs_users JOIN cs_coffees ON cuid=uid
         GROUP BY cuid LIMIT %d",
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
    return $retval;
}
?>
