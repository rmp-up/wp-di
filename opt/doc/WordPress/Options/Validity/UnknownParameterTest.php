<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * UnknownOption.php
 *
 * LICENSE: This source file is created by the company around Mike Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package    wp-di
 * @copyright 2021 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Options\Validity;

use RmpUp\WpDi\Test\WordPress\Options\OptionsTestCase;

/**
 * UnknownOption
 *
 * @internal
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class UnknownParameterTest extends OptionsTestCase
{
    public function testDoesNotFail()
    {
        // This should not throw an exception
        $option = uniqid('', true);
        static::assertNotEquals($option, $this->option($option));
    }

    public function testReturnsInternalDefaultValue()
    {
        static::assertNull($this->option(uniqid('', true)));
    }

    public function testInvalidReferencesNeitherFail()
    {
        $this->pimple['invalid'] = static function ($container) {
            return $container[uniqid('', true)];
        };

        static::assertNull($this->option('invalid'));
    }
}