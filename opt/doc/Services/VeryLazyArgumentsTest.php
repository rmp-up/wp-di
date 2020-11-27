<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * VeryLazyArgumentsTest.php
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

use MyWidget;
use RmpUp\WpDi\Helper\LazyPimple;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\AbstractTestCase;
use RmpUp\WpDi\Test\Mirror;
use SomeRepository;

/**
 * Lazy arguments
 *
 * By now all services are written like this:
 *
 * ```yaml
 * services:
 *   SomeRepository: ~
 *
 *   MyWidget:
 *     arguments:
 *       - '@SomeRepository'
 * ```
 *
 * The problem is that this may not be as lazy as expected.
 * Actually WordPress forces to load the Widget
 * and the possibly expensive Repository will be loaded too.
 * This way WordPress breaks laziness for:
 *
 * * Widgets (`register_widgets`)
 * * Block types (`register_block_type`)
 * * possibly more
 *
 * and generates some unnecessary overhead.
 *
 *   Widgets would be turned into objects during registration
 * (see `register_widgets`)
 * no matter if they are used or not.
 * Block types also can be an immediate instance,
 * which would generate costs as all injected services also need to wake up
 * and be instantiated.
 * Other candidates can be found by looking into each register_* function
 * and see how/when WordPress tries to use instances.
 *
 * On the one hand we want to give WordPress enough to do its job
 * but on the other hand we don't want all those costs (e.g. time and memory).
 * Here is one solution:
 *
 * ```yaml
 * services:
 *   SomeRepository: ~
 *
 *   MyWidget:
 *     lazy_arguments: true
 *     arguments:
 *       - '@SomeRepository'
 * ```
 *
 * When WordPress (or any other thing) now creates an instance of "MyWidget"
 * then the container will deliver is but keep the injected services lazy
 * thanks to the `lazy_arguments: true` flag.
 * So instead of loading everything (MyWidget and the expensive SomeRepository)
 * with every request,
 * we now only load the necessary layer (MyWidget)
 * while the injected Arguments (SomeRepository) remain lazy.
 * This can spare a lot of time and memory for the mentioned cases.
 *
 * **WARNING: When you use lazy arguments then the targeted class (here: `MyWidget`)
 * can no longer use type-hints in the signature of the constructor,
 * because a proxy-class will be injected instead of the real ones
 * (here: `LazyPimple` instead of `SomeRepository`).**
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class VeryLazyArgumentsTest extends AbstractTestCase
{
    public function testDisabledLazyArgsDoesNothing()
    {
        $this->registerServices(0);

        static::assertFalse(
            $this->isServiceFrozen($this->pimple, SomeRepository::class),
            'something already triggered the build - stopping test'
        );

        // trigger
        $this->pimple[MyWidget::class];

        static::assertTrue(
            $this->isServiceFrozen($this->pimple, SomeRepository::class),
            'sub service should have been instantiated'
        );
    }

    public function testEnablingLazyArgumentsPostponesObjectCreation()
    {
        $this->registerServices(1);

        static::assertFalse(
            $this->isServiceFrozen($this->pimple, SomeRepository::class),
            'something already triggered the build - stopping test'
        );

        $this->pimple[MyWidget::class]; // trigger

        static::assertFalse(
            $this->isServiceFrozen($this->pimple, SomeRepository::class),
            'sub-service should not be instantiated already'
        );

        /** @var Mirror $mirror */
        $mirror = $this->pimple[MyWidget::class];

        /** @var LazyPimple $lazyArgument */
        $lazyArgument = $mirror->getConstructorArgs()[0];

        // trigger sub-service
        $lazyArgument->foo();

        static::assertTrue(
            $this->isServiceFrozen($this->pimple, SomeRepository::class),
            'sub-service should have been instantiated now'
        );
    }
}