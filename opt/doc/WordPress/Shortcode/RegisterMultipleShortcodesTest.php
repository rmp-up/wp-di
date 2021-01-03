<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RegisterMultipleShortcodesTest.php
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

use MyOwnShortcode;
use RmpUp\WpDi\Test\WordPress\ShortcodeTestCase;

/**
 * Registering multiple shortcodes
 *
 * Sometimes you may want multiple shortcodes to do the very same thing.
 * For this or similar scenarios you can map shortcodes to the handler method:
 *
 * ```yaml
 * services:
 *   MyOwnShortcode:
 *     shortcode:
 *       i_am_the_danger: deprecatedUsage
 *       is_that_you: __invoke
 * ```
 *
 * Our MyOwnShortcode-Class looks almost like this:
 *
 * ```
 * class MyOwnShortcode {
 *   function deprecatedUsage() // supporting "i_am_the_danger"
 *
 *   function __invoke() // for "is_that_you" shortcode
 * }
 * ```
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class RegisterMultipleShortcodesTest extends ShortcodeTestCase
{
    protected function setUp()
    {
        parent::setUp();

        self::assertShortcodeNotExists('i_am_the_danger');
        self::assertShortcodeNotExists('is_that_you');
    }

    public function testShortcodeMapToMethod()
    {
        $this->registerServices();

        static::assertEmpty(MyOwnShortcode::_history('deprecatedUsage'));
        do_shortcode('[i_am_the_danger]');
        static::assertEquals(['', '', 'i_am_the_danger'], MyOwnShortcode::_history('deprecatedUsage')[0]['arguments']);

        static::assertEmpty(MyOwnShortcode::_history('__invoke'));
        do_shortcode('[is_that_you]');
        static::assertEquals(['', '', 'is_that_you'], MyOwnShortcode::_history('__invoke')[0]['arguments']);
    }

    protected function tearDown()
    {
        if (shortcode_exists('i_am_the_danger')) {
            remove_shortcode('i_am_the_danger');
        }

        if (shortcode_exists('is_that_you')) {
            remove_shortcode('is_that_you');
        }

        parent::tearDown();
    }
}