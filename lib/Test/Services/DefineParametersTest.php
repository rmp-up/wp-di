<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * KeepStringsTest.php
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
 * @copyright 2020 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\Services;

use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * Parameters
 *
 * Primitives values that shall remain as they are can be added as parameters:
 *
 * ```yaml
 * parameters:
 *   dates: 50
 *
 * services:
 *   SomeThing:
 *     arguments: '%dates%'
 * ```
 *
 * The first argument of the class `SomeThing` is a reference
 * to the parameter, so in the end it is the same as a `new SomeThing(50)` call.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class DefineParametersTest extends SanitizerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->pimple->register(new Provider($this->yaml(0)));
    }

    public function testParameterDefinition()
    {
        static::assertSame(50, $this->pimple['%dates%']);
    }
}