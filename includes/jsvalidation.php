<?php
/*
 * Defines some JavaScript validators.
 */

/**
 * Render a JavaScript function to sanitize not-empty strings.
 */
function js_sanitize_not_empty() {
?>
<script type="text/javascript">
    function sanitize_not_empty(fieldspec, message) {
        var nefield = $(fieldspec);
        var nevalue = $.trim(nefield.val());
        if (nevalue.length == 0) {
            alert(message);
            nefield.focus();
            return false;
        }
        nefield.val(nevalue);
        return true;
    }
</script>
<?php
}

/**
 * Render a JavaScript function to sanitize email addresses.
 */
function js_sanitize_email() {
?>
<script type="text/javascript">
    var emailpat = /^([A-Za-z0-9._%+-]+)@([^@]+)$/;

    function sanitize_email(fieldspec, mandatory) {
        mandatory = typeof mandatory !== 'undefined' ? mandatory : true;
        var emfield = $(fieldspec);
        var emvalue = $.trim(emfield.val());
        if ((emvalue.length == 0) && mandatory) {
            alert('Email address must not be empty!');
            emfield.focus();
            return false;
        }
        if (emailpat.test(emvalue)) {
            emfield.val(emvalue);
            return true;
        }
        alert('Email address must contain a local and a domain part separated by on @ sing!');
        emfield.focus();
        return false;
    }
</script>
<?php
}

/**
 * Render a JavaScript function to sanitize username values.
 */
function js_sanitize_username() {
?>
<script type="text/javascript">
    var usernamepat = /^[a-z][a-z0-9_-]{1,29}$/;

    function sanitize_username(fieldspec) {
        var unfield = $(fieldspec);
        var unvalue = $.trim(unfield.val());
        if (unvalue.length == 0) {
            alert('Username must not be empty!');
            unfield.focus();
            return false;
        }
        if (usernamepat.test(unvalue)) {
            unfield.val(unvalue);
            return true;
        }
        alert('Invalid username! A username has at least 3 characters, starting with a letter. It may consist of letters, digits, hypens and underscores.');
        unfield.focus();
        return false;
    }
</script>
<?php
}

/**
 * Render a JavaScript function to sanitize datetime values.
 */
function js_sanitize_datetime() {
?>
<script type="text/javascript">
    function pad(n) {
        return n<10 ? '0'+n : n;
    }

    function coffeetime(d) {
        return d.getFullYear() + '-' +
           pad(d.getMonth() + 1) +'-' +
           pad(d.getDate()) + ' ' +
           pad(d.getHours()) + ':' +
           pad(d.getMinutes()) +':' +
           pad(d.getSeconds());
    }

    var datetimepat = /^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})\ ([0-9]{1,2}):([0-9]{1,2})(|:([0-9]{1,2}))$/;

    function sanitize_datetime(fieldspec) {
        var dtfield = $(fieldspec);
        var dtval = $.trim(dtfield.val());
        if (dtval.length == 0) {
            dtval = coffeetime(new Date());
            dtfield.val(dtval);
        }
        if (datetimepat.test(dtval)) {
            dtfield.val(dtval);
            return true;
        }
        alert('No valid date/time information. Expected format YYYY-mm-dd HH:MM:ss');
        dtfield.focus();
        return false;
    }
</script>
<?php
}

/**
 * Render a JavaScript function to sanitize a pair of passwords.
 */
function js_sanitize_password() {
?>
<script type="text/javascript">
    function sanitize_password(pwfieldspec, repfieldspec) {
        var pwfield = $(pwfieldspec);
        var repfield = $(repfieldspec);
        var pwval = $.trim(pwfield.val());
        var repval = $.trim(repfield.val());

        pwfield.val(pwval);
        repfield.val(repval);

        if (pwval.length < 8) {
            alert('Password must be at least 8 characters long!');
            pwfield.focus();
            return false;
        }
        if (pwval != repval) {
            alert('Passwords must match!');
            repfield.focus();
            return false;
        }
        return true;
    }
</script>
<?php
}

/**
 * Render a JavaScript function to sanitize a string.
 */
function js_sanitize_string() {
?>
<script type="text/javascript">
    function sanitize_string(fieldspec, mandatory, fieldname) {
        mandatory = typeof mandatory !== 'undefined' ? mandatory : true;
        fieldname = typeof fieldname !== 'undefined' ? fieldname : 'Field';
        var stfield = $(fieldspec);
        var stvalue = $.trim(stfield.val());
        if ((stvalue.length == 0) && mandatory) {
            alert(fieldname + ' must not be empty!');
            stfield.focus();
            return false;
        }
        return true;
    }
</script>
<?php
}
?>
