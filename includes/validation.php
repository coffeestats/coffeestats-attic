<?php
/*
 * Validation functions.
 */

/**
 * Validate a single password or a pair of passwords.
 */
function validate_password($password, $repeat=NULL) {
    if (empty($password)) {
        flash('Password must not be empty!', FLASH_ERROR);
        return FALSE;
    }
    if (($repeat !== NULL) && (strcmp($password, $repeat) !== 0)) {
        flash('Passwords in both fields must be the same!', FLASH_ERROR);
        return FALSE;
    }
    return TRUE;
}

/**
 * Validate a combined datetime string.
 */
function validate_datetime($datetime) {
    if (empty($datetime)) {
        flash('No valid date/time information. Must not be empty!', FLASH_ERROR);
        return FALSE;
    }
    if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}\ [0-9]{2}:[0-9]{2}/', $datetime)) {
        flash('No valid date/time information. Expected format YYYY-mm-dd HH:MM', FLASH_ERROR);
        return FALSE;
    }
    return TRUE;
}
?>
