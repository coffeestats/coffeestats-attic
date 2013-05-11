<?php
/*
 * UnitTests for validation.php.
 */

include_once(sprintf('%s/../../includes/validation.php', dirname(__FILE__)));

class ValidationTest extends PHPUnit_Framework_TestCase {

    public function test_sanitize_password_empty() {
        $emptyvariants = array("", NULL, "   ");
        foreach ($emptyvariants as $password) {
            $this->assertFalse(sanitize_password($password));
            $this->assertEquals(
                array(FLASH_ERROR, 'Password must not be empty!'),
                pop_flash());
        }
    }

    public function test_sanitize_password_short() {
        $shortvariants = array("a", "abcdefg", " abcdefg ");
        foreach ($shortvariants as $password) {
            $this->assertFalse(sanitize_password($password));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Password must be at least 8 characters long'),
                pop_flash());
        }
    }

    public function test_sanitize_password_wrong_repeat() {
        $this->assertFalse(sanitize_password('abcdefgh', 'hgfedcba'));
        $this->assertEquals(
            array(FLASH_ERROR, 'Passwords in both fields must be the same!'),
            pop_flash());
    }

    public function test_sanitize_password_good() {
        $goodpasswords = array('aBcdefgH', 'ab  cd  ef', 'äÄasedfer');
        foreach ($goodpasswords as $password) {
            $this->assertEquals($password, sanitize_password($password));
            $this->assertNull(pop_flash());
        }
        foreach ($goodpasswords as $password) {
            $this->assertEquals(
                $password, sanitize_password($password, $password));
            $this->assertNull(pop_flash());
        }
    }

    public function test_sanitize_datetime_empty() {
        $emptyvariants = array("", NULL, "   ");
        foreach ($emptyvariants as $datetime) {
            $this->assertFalse(sanitize_datetime($datetime));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'No valid date/time information. Must not be empty!'),
                pop_flash());
        }
    }

    public function test_sanitize_datetime_bad() {
        $badvariants = array('X', '21,3', '21-2-3 01:2');
        foreach ($badvariants as $datetime) {
            $this->assertFalse(sanitize_datetime($datetime));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'No valid date/time information. ' .
                    'Expected format YYYY-mm-dd HH:MM'),
                pop_flash());
        }
    }

    public function test_sanitize_datetime_good() {
        $goodvariants = array(
            array('2013-08-10 20:15:01', '2013-08-10 20:15:01'),
            array('2013-08-10 20:15:01', '2013-8-10 20:15:1'),
            array('2013-08-10 20:15:01', '  2013-8-10 20:15:1 '),
        );
        foreach ($goodvariants as $good) {
            $this->assertEquals($good[0], sanitize_datetime($good[1]));
            $this->assertNull(pop_flash());
        }
    }

    public function test_sanitize_email_empty() {
        $emptyvariants = array("", NULL, "   ");
        foreach ($emptyvariants as $email) {
            $this->assertFalse(sanitize_email($email));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Email address must not be empty!'),
                pop_flash());
        }
    }

    public function test_sanitize_email_wrongre() {
        $wrongvariants = array(
            '@', 'noat_domain', 'local@', '@domain', 'blÖ@domain');
        foreach ($wrongvariants as $email) {
            $this->assertFalse(sanitize_email($email));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Email address must contain a local and a domain part ' .
                    'separated by one @ sign!'),
                pop_flash());
        }
    }

    public function test_sanitize_email_baddomain() {
        $wrongvariants = array(
            'test@domain', 'test+bla@domain');
        foreach ($wrongvariants as $email) {
            $this->assertFalse(sanitize_email($email));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Email address must contain a valid domain part!'),
                pop_flash());
        }
    }

    public function test_sanitize_email_good() {
        $goodvariants = array(
            array(
                'test@coffeestats.org',
                'test@coffeestats.org'),
            array(
                'test+person@coffeestats.org',
                'test+person@coffeestats.org'),
            array(
                'test_with-spaces@coffeestats.org',
                '  test_with-spaces@coffeestats.org'),
        );
        foreach ($goodvariants as $good) {
            $this->assertEquals($good[0], sanitize_email($good[1]));
            $this->assertNull(pop_flash());
        }
    }
}
?>
