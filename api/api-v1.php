<?php
if (!isset($_GET['q'])) {
    header("Status: 400 Bad Request");
    header("Content-Type: text/plain");
    print("400 Bad Request");
    exit();
}

$query = explode('/', $_GET['q']);

$resource = $query[0];

/**
 * Format the given output object as JSON string.
 */
function format_output($object) {
    header("Content-Type: application/json; charset=utf8");
    print json_encode($object);
}

include_once('../auth/config.php');
include_once('../includes/queries.php');
include_once('../includes/common.php');

/**
 * Check whether user is authenticated.
 */
function check_authentication() {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (isset($_SESSION['login_id'])) {
        return;
    }
    // TODO: implement some API key mechanism for non-interactive users
    header('Status: 403 Forbidden');
    header('Content-Type: text/plain; charset=utf8');
    print('You need to authenticate to use this service.');
    exit();
}

/**
 * Get a list of $count random users.
 */
function get_random_users($count) {
    check_authentication();
    $userdata = random_users($count);
    $retval = array();
    foreach ($userdata as $user) {
        array_push($retval, array(
            'username' => $user['ulogin'],
            'name' => trim(sprintf("%s %s", $user['ufname'], $user['uname'])),
            'location' => $user['ulocation'],
            'profile' => public_url($user['ulogin']),
            'coffees' => $user['coffees'],
            'mate' => $user['mate']));
    }
    return $retval;
}

switch ($resource) {
case "random-users":
    if (count($query) > 1) {
        $count = intval($query[1]);
    }
    else {
        $count = 5;
    }
    format_output(get_random_users($count));
    break;
default:
    header("Status: 404 Not Found");
    header("Content-Type: text/plain");
    print("404 Not Found");
    exit();
}
?>
