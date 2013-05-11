<?php
/*
 * Defines some JavaScript validators.
 */

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
?>
