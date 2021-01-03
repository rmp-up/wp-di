<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OptionsTest.php
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

namespace RmpUp\WpDi\Test\Primitives;

use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\AbstractTestCase;
use RmpUp\WpDi\Test\Mirror;

/**
 * Options
 *
 * WordPress-Options can be fetched using `get_options`.
 * The "options" section is needed to register specific options
 * so that they can be used in services:
 *
 * ```yaml
 * options:
 *   # Name of the option
 *   - my_own_option
 *
 * services:
 *   SomeThing:
 *     arguments: [ '%my_own_option%' ]
 * ```
 *
 *   Such basic service definition does load the option value
 * and inject it into the service.
 * This way you decouple information from the plugin/theme
 * and allow the user to configure them in the WordPress-Backend
 * (e.g. via "wp-admin/options.php").
 *
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class OptionsTest extends AbstractTestCase
{
    private $expectedValue;

    protected function setUp()
    {
        parent::setUp();

        $this->expectedValue = uniqid('', true);

        $this->mockOption('my_own_option')
            ->expects($this->atLeastOnce())
            ->willReturn($this->expectedValue);
    }

    public function testOptionsIntro()
    {
        (new Provider())($this->yaml(0), $this->pimple);

        /** @var Mirror $mirror */
        $mirror = $this->pimple[\SomeThing::class];

        static::assertInstanceOf(Mirror::class, $mirror, 'Failure creating service');
        static::assertSame([$this->expectedValue], $mirror->getConstructorArgs());
    }
}