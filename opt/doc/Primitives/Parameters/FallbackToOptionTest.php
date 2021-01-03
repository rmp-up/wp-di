<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FallbackToOptionTest.php
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

namespace RmpUp\WpDi\Test\Primitives\Parameters;

use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\AbstractTestCase;
use RmpUp\WpDi\Test\Mirror;
use SomeThingElse;

/**
 * Auto-Fallback to options
 *
 * Using parameters that do NOT exist ...
 *
 * ```yaml
 * parameters:
 *   gdpr: true
 *
 * services:
 *   SomeThing:
 *     arguments:
 *       - "%gdpr%"
 *       - "%blog_public%"
 * ```
 *
 * ... like this "blog_public"
 * will make wp-di look-up the option with the same name.
 * For the above example this will result into:
 *
 * ```
 * new SomeThing( true, get_option("blog_public") );
 * ```
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class FallbackToOptionTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->mockOption('blog_public')->expects($this->atLeastOnce())->willReturn('nice');

        $this->registerServices();
    }

    public function testFetchedOptionInstead()
    {
        /** @var Mirror $thing */
        $thing = $this->pimple[\SomeThing::class];

        static::assertSame(
            [
                true,
                'nice'
            ],
            $thing->getConstructorArgs()
        );
    }
}