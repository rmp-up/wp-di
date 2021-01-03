<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PrimitivesTest.php
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

namespace RmpUp\WpDi\Test;

/**
 * Global parameters
 *
 * By now a service had its arguments directly injected:
 *
 * ```yaml
 * services:
 *   SomeThing:
 *     arguments: [ 1337, '2020-02-29' ]
 *   SomeThingElse:
 *     arguments: [ '2020-02-29', 42 ]
 * ```
 *
 * But here we seem to have some often used information (the "2020-02-29").
 * To keep things maintainable this can go into it's own section
 * (e.g. "parameters"):
 *
 * ```yaml
 * parameters:
 *   leap_day: 2020-02-29
 *
 * services:
 *   SomeThing:
 *     arguments: [ 1337, '%leap_day%' ]
 *   SomeThingElse:
 *     arguments: [ '%leap_day%', 42 ]
 * ```
 *
 * In this part you'll learn about further siblings of "services"
 * like "parameters",
 * "options" (WordPress-Options),
 * "templates" (see `locate_template()`)
 * and other possibilities.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class PrimitivesTest extends AbstractTestCase
{
    public function testNothing()
    {
        static::assertTrue(true);
    }
}