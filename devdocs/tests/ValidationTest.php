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

    public function test_sanitize_string_empty() {
        $emptyvariants = array("", NULL, "   ");
        foreach ($emptyvariants as $variant) {
            $this->assertFalse(sanitize_string($variant));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Field must not be empty!'),
                pop_flash());
        }
        foreach ($emptyvariants as $variant) {
            $this->assertFalse(sanitize_string($variant, TRUE, 'Yada'));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Yada must not be empty!'),
                pop_flash());
        }
        foreach ($emptyvariants as $variant) {
            $this->assertEquals("", sanitize_string($variant, FALSE));
            $this->assertNull(pop_flash());
        }
    }

    public function test_sanitize_string_good() {
        $goodvariants = array(
            array('Test', 'Test'),
            array('Täst', ' Täst '),
            array('<Test', '<Test'),
        );
        foreach ($goodvariants as $variant) {
            $this->assertEquals($variant[0], sanitize_string($variant[1]));
            $this->assertNull(pop_flash());
        }
    }

    public function test_sanitize_notempty_empty() {
        $emptyvariants = array("", NULL, "   ");
        foreach ($emptyvariants as $variant) {
            $this->assertFalse(sanitize_notempty($variant, 'Yada'));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Yada must not be empty!'),
                pop_flash());
        }
    }

    public function test_sanitize_notempty_good() {
        $goodvariants = array(
            array('Test', 'Test'),
            array('Täst', ' Täst '),
            array('<Test', '<Test'),
        );
        foreach ($goodvariants as $variant) {
            $this->assertEquals($variant[0], sanitize_notempty($variant[1], 'Yada'));
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

    public function test_sanitize_username_empty() {
        $emptyvariants = array("", NULL, "   ");
        foreach ($emptyvariants as $variant) {
            $this->assertFalse(sanitize_username($variant));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Username must not be empty!'),
                pop_flash());
        }
    }

    public function test_sanitize_username_bad() {
        $badvariants = array(
            '.abcd', '_abcd', 'abcd#', 'abcd.',
            'abcdefghijklmnopqrstuvwxyz0123456789');
        foreach ($badvariants as $variant) {
            $this->assertFalse(sanitize_username($variant));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Invalid username! A username has at least 3 ' .
                    'characters, starting with a letter. It may consist of ' .
                    'lowercase letters, digits, hypens and underscores.'),
                pop_flash());
        }
    }

    public function test_sanitize_username_good() {
        $goodvariants = array(
            array('tester', 'tester'),
            array('tester', ' Tester'),
            array('tester2', 'tester2'),
            array('test_user-20', 'Test_User-20'),
            array(
                'abcdefghijklmnopqrstuvwxyz0123',
                'AbCdEfGhIjKlMnOpQrStUvWxYz0123'),
        );
        foreach ($goodvariants as $variant) {
            $this->assertEquals($variant[0], sanitize_username($variant[1]));
            $this->assertNull(pop_flash());
        }
    }

    public function test_sanitize_md5value_empty() {
        $emptyvariants = array("", NULL, "   ");
        foreach ($emptyvariants as $variant) {
            $this->assertFalse(sanitize_md5value($variant));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'MD5 value must not be empty!'),
                pop_flash());
        }
        foreach ($emptyvariants as $variant) {
            $this->assertFalse(sanitize_md5value($variant, 'Yada'));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Yada must not be empty!'),
                pop_flash());
        }
    }

    public function test_sanitize_md5value_bad() {
        $badvariants = array('012345', 'abcdefghijklmnopqrstuvwxyz012345');
        foreach ($badvariants as $variant) {
            $this->assertFalse(sanitize_md5value($variant));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Invalid MD5 value'),
                pop_flash());
        }
        foreach ($badvariants as $variant) {
            $this->assertFalse(sanitize_md5value($variant, 'Yada'));
            $this->assertEquals(
                array(
                    FLASH_ERROR,
                    'Invalid Yada'),
                pop_flash());
        }
    }

    public function test_sanitize_md5value_good() {
        $goodvariants = array(
            array(md5('test'), md5('test')),
            array(md5('test'), strtoupper(md5('test'))),
            array(md5('test'), sprintf(' %s ', md5('test'))),
        );
        foreach ($goodvariants as $variant) {
            $this->assertEquals($variant[0], sanitize_md5value($variant[1]));
            $this->assertNull(pop_flash());
        }
    }
}
?>
