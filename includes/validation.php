<?php
/*
 * Validation functions.
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
?>
