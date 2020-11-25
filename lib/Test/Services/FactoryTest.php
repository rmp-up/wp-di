<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Factory.php
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

namespace RmpUp\WpDi\Test\Services;

use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\ServiceDefinition;
use RmpUp\WpDi\Test\AbstractTestCase;
use SomeThingElse;

/**
 * Factory
 *
 * Services might not always be the same in any context.
 * For example a API call is way different from a CLI or Browser call.
 * In those cases you may want to inject a service slightly different
 * depending on the context or environment.
 * Factories can be used to dynamically create those services/objects:
 *
 * ```yaml
 * services:
 *   MyFactory: ~
 *
 *   SomeThingElse:
 *     factory: "@MyFactory"
 * ```
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class FactoryTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->registerServices();
    }

    public function testDefinition()
    {
        static::assertInstanceOf(ServiceDefinition::class, $this->pimple->raw(SomeThingElse::class));

        $result = $this->pimple[SomeThingElse::class];

        // Invoked MyFactory with nothing
        static::assertNotInstanceOf(SomeThingElse::class, $result);
        static::assertEquals([], $result['invoked']);
    }
}