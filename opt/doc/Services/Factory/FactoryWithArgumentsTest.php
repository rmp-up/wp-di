<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FactoryWithArgumentsTest.php
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
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\Services\Factory;

use RmpUp\WpDi\Test\AbstractTestCase;
use SomeThing;

/**
 * Using arguments
 *
 * Of course the factory needs to know some things of the environment.
 * Usually the arguments are injected in the new object
 * but in case of a factory-pattern the arguments become
 * the parameter of the factory callback:
 *
 * ```yaml
 * parameters:
 *   line: wand
 *   circle: stone
 *   triangle: cloak
 *
 * services:
 *   MyFactory: ~
 *
 *   SomeThing:
 *     factory: "@MyFactory"
 *     arguments: [ "%line%", "%circle%", "%triangle%", "404 logic not found" ]
 * ```
 *
 * Here the callback is `MyFactory->__invoke("wand", ...)`.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class FactoryWithArgumentsTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->registerServices();
    }

    public function testFactoryUsingArguments()
    {
        static::assertEquals('@MyFactory', $this->pimple->raw('SomeThing')['factory']);

        $result = $this->pimple[SomeThing::class];
        static::assertNotInstanceOf(SomeThing::class, $result);

        // Proofs that SomeThing::__invoke has been called
        // due to how the Mirror::__invoke works.
        static::assertEquals(
            ['wand', 'stone', 'cloak', '404 logic not found'],
            $result['invoked']
        );
    }
}