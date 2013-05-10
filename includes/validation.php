<?php
/*
 * Validation functions.
 */

include_once(sprintf('%s/../lib/AntiXSS.php', dirname(__FILE__)));

/**
 * Validate a single password or a pair of passwords.
 */
function sanitize_password($password, $repeat=NULL) {
    $password = trim($password);
    if (empty($password)) {
        flash('Password must not be empty!', FLASH_ERROR);
        return FALSE;
    }
    if (strlen($password) < 8) {
        flash('Password must be at least 8 characters long', FLASH_ERROR);
        return FALSE;
    }
    if ($repeat !== NULL) {
        $repeat = trim($repeat);
        if (strcmp($password, $repeat) !== 0) {
            flash('Passwords in both fields must be the same!', FLASH_ERROR);
            return FALSE;
        }
    }
    return $password;
}

/**
 * Validate a combined datetime string.
 */
function validate_datetime($datetime) {
    if (empty($datetime)) {
        flash('No valid date/time information. Must not be empty!', FLASH_ERROR);
        return FALSE;
    }
    if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}\ [0-9]{2}:[0-9]{2}(|:[0-9]{2})$/', $datetime)) {
        flash('No valid date/time information. Expected format YYYY-mm-dd HH:MM', FLASH_ERROR);
        return FALSE;
    }
    return TRUE;
}
?>
