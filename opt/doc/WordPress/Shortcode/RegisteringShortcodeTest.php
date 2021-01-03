<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RegisteringShortcodeTest.php
 *
 * LICENSE: This source file is created by the company around M. Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package   wp-di
 * @copyright 2021 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Shortcode;

use RmpUp\WpDi\Compiler\AlreadyExistsException;
use RmpUp\WpDi\Test\WordPress\ShortcodeTestCase;

/**
 * Registering shortcode
 *
 * ```yaml
 * services:
 *   MyOwnShortcode:
 *     shortcode: i_am_the_danger
 * ```
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class RegisteringShortcodeTest extends ShortcodeTestCase
{
    public function testRegistersShortcode()
    {
        self::assertShortcodeNotExists('i_am_the_danger');

        $this->registerServices();

        self::assertShortcodeExists('i_am_the_danger');
    }

    /**
     * When the shortcode is already registered,
     * then it shall throw an exception.
     *
     * @internal
     */
    public function testShortcodeIsAlreadyRegistered()
    {
        self::assertShortcodeNotExists('i_am_the_danger');

        add_shortcode('i_am_the_danger', '__return_true');
        self::assertShortcodeExists('i_am_the_danger');

        $this->expectException(AlreadyExistsException::class);
        $this->registerServices();
    }

    protected function tearDown()
    {
        if (shortcode_exists('i_am_the_danger')) {
            remove_shortcode('i_am_the_danger');
        }

        parent::tearDown();
    }
}