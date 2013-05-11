<?php
/*
 * Validation functions.
 */
include_once(sprintf('%s/common.php', dirname(__FILE__)));

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

/**
 * Validate an e-mail address and return a sanitized version.
 */
function sanitize_email($email, $optional=FALSE) {
    $email = trim($email);
    if (empty($email)) {
        flash('Email address must not be empty!', FLASH_ERROR);
        return $optional ? "" : FALSE;
    }
    if (!preg_match('/^([A-Za-z0-9._%+-]+)@([^@]+)$/', $email, $matches)) {
        flash('Email address must contain a local and a domain part separated by one @ sign!', FLASH_ERROR);
        return FALSE;
    }
    $localpart = $matches[1];
    $domainpart = $matches[2];
    if (getmxrr($domainpart, $mxhosts) === FALSE) {
        flash('Email address must contain a valid domain part!', FLASH_ERROR);
        return FALSE;
    }
    return sprintf("%s@%s", $localpart, $domainpart);
}

/**
 * Validate a username and return a sanitized version.
 */
function sanitize_username($username) {
    $username = strtolower(trim($username));
    if (empty($username)) {
        flash('Username must not be empty!', FLASH_ERROR);
        return FALSE;
    }
    if (!preg_match('/^[a-z][a-z0-9_-]{1,29}$/', $username)) {
        flash('Invalid username! A username has at least 3 characters, starting with a letter. It may consist of lowercase letters, digits, hypens and underscores.', FLASH_ERROR);
        return FALSE;
    }
    return $username;
}

/**
 * Validate a hexadecimal encoded MD5 hash value and return a sanitized
 * version.
 */
function sanitize_md5value($value, $name='MD5 value') {
    $value = strtolower(trim($value));
    if (empty($value)) {
        flash(sprintf('%s must not be empty!', $name), FLASH_ERROR);
        return FALSE;
    }
    if (!preg_match('/^[a-f0-9]{32}$/', $value)) {
        flash(sprintf('Invalid %s', $name), FLASH_ERROR);
        return FALSE;
    }
    return $value;
}
?>
