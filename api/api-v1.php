<?php
define('CATEGORY_API', 'api');

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

include_once('../includes/queries.php');
include_once('../includes/common.php');

/**
 * Put a flash message into the API category.
 *
 * @param message
 *        The message to store into the flash buffer.
 * @param level
 *        The severity level of this message, pass one of the FLASH_*
 *        constants, defaults to FLASH_ERROR.
 */
function api_flash($message, $level=FLASH_ERROR) {
	return flash($message, $level, CATEGORY_API);
}

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
 * Check for authentication using an API token
 *
 * @return The userinfo array, if the user is authenticated, FALSE otherwise
 */
function check_token_authentication() {
	if (!isset($_SESSION)) {
		session_start();
	}

	// Validate username and API token
	if (!isset($_POST['u'])) {
		api_flash('No username was given');
		return FALSE;
	}
	if (!isset($_POST['t'])) {
		api_flash('No API token was given');
		return FALSE;
	}
	if ((($user = sanitize_username($_POST['u'], CATEGORY_API)) === FALSE) ||
		(($token = sanitize_md5value($_POST['t'], 'API token', CATEGORY_API)) === FALSE)) {
		// the sanitize functions already flash
		return FALSE;
	}
	if (($userinfo = find_user_uid_token_login_and_timezone_by_login_and_token($user, $token)) === NULL) {
		api_flash('Invalid username or API token');
		return FALSE;
	}

	// Warn if the timezone isn't set
	if ($userinfo['utimezone'] == NULL) {
		api_flash('Your timezone is not set, please set it from the webinterface!', FLASH_WARNING);
	}

	return $userinfo;
}

/**
 * Update the given object with all messages currently pending in the flash
 * buffer for the CATEGORY_API category. For each severity level that has at
 * least one message pending, a correspondingly-named property will be created
 * in the given object. This property will be an array of strings, which are
 * the messages stored in the flash buffer.
 *
 * @param json
 *        The object that should be updated with the state from the flash
 *        buffer.
 * @return The given object.
 */
function api_flash_get(object &$json) {
	if (peek_flash(CATEGORY_API)) {
		while (($message = pop_flash(CATEGORY_API)) !== NULL) {
			list($level, $text) = $message;
			if (!isset($json->$level)) {
				$json->$level = array();
			}
			array_push($json->$level, $message);
		}
	}

	return $json;
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

/**
 * Register a new drink for a user. Requires API token authentication, i.e. the
 * keys `u' and `t' to be set in the POST request. Also requires the field
 * `beverage' to be one of `coffee' or `mate' and the field `time' to hold the
 * time of consumption formatted as YYYY-mm-dd HH:MM and optionally :SS.
 *
 * @return An object to be formatted to JSON and returned to the user.
 */
function add_drink() {
	$json = new stdClass();

	$userinfo = check_token_authentication();
	if ($userinfo === FALSE) {
		header('Status: 403 Forbidden');
		api_flash('API operation requires authentication');
		$json->success = FALSE;
	} else {
		// Validate the beverage and time fields
		if (!isset($_POST['beverage'])) {
			header('Status: 400 Bad Request');
			api_flash("`beverage' field missing. You must specify one of `coffee' or `mate'.");
			$json->success = FALSE;
		} elseif (($type = sanitize_string($_POST['beverage'], TRUE, 'Beverage', CATEGORY_API)) === NULL) {
			header('Status: 400 Bad Request');
			// sanitize already called flash()
			$json->success = FALSE;
		} elseif (!isset($_POST['time'])) {
			header('Status: 400 Bad Request');
			api_flash("`time' field missing. You must specify the time as YYYY-mm-dd HH:MM (and optionally :SS)");
			$json->success = FALSE;
		} elseif (($time = sanitize_datetime($_POST['time'], CATEGORY_API)) === FALSE) {
			header('Status: 400 Bad Request');
			// sanitize already called flash()
			$json->success = FALSE;
		} else {
			// Store the beverage
			switch ($type) {
				case 'coffee':
					$json->success = register_coffee($userinfo['uid'], $time, $userinfo['utimezone'], CATEGORY_API);
				case 'mate':
					$json->success = register_mate($userinfo['uid'], $time, $userinfo['utimezone'], CATEGORY_API);
				default:
					header('Status: 400 Bad Request');
					api_flash("`beverage' contains an invalid value. Acceptable values are `coffee' and `mate'.");
					$json->success = FALSE;
			}
		}
	}

	return api_flash_get($json);
}

switch ($resource) {
case "random-users":
    $count = isset($_GET['count']) ? intval($_GET['count']) : 5;
    format_output(get_random_users($count));
    break;
case "add-drink":
	format_output(add_drink());
	break;
default:
    header("Status: 404 Not Found");
    header("Content-Type: text/plain");
    print("404 Not Found");
    exit();
}
?>
