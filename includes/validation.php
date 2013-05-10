<?php
/*
 * Validation functions.
 */

/**
 * Validate a single password or a pair of passwords and return a sanitized
 * version of the password.
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
 * Validate a combined datetime string and return a sanitized version of the
 * datetime value.
 */
function sanitize_datetime($datetime) {
    $datetime = trim($datetime);
    if (empty($datetime)) {
        flash('No valid date/time information. Must not be empty!', FLASH_ERROR);
        return FALSE;
    }
    if (!preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})\ ([0-9]{1,2}):([0-9]{1,2})(|:([0-9]{1,2}))$/', $datetime, $matches)) {
        flash('No valid date/time information. Expected format YYYY-mm-dd HH:MM', FLASH_ERROR);
        return FALSE;
    }
    return sprintf(
        "%04d-%02d-%02d %02d:%02d:%02d",
        $matches[1], $matches[2], $matches[3],
        $matches[4], $matches[5], isset($matches[7]) ? $matches[7] : 0);
}
?>
