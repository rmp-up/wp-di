<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AutoResolvingProviderTest.php
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

namespace RmpUp\WpDi\Test;

use ArrayObject;
use InvalidArgumentException;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Provider\WordPress\Actions;
use RmpUp\WpDi\Provider\WordPress\PostTypes;

/**
 * Introduction
 *
 *   Writing plugins can be a pain as everyone wants to do it different
 * and aim for different structures.
 * But what most of us have in common is that we want simplicity
 * and get away from WordPress to a more agnostic OOP workflow
 * to hold up testability, maintainability and all the other terms of software quality.
 *
 * We solve all of this by a simple service definition like this:
 *
 *
 * ```yaml
 * options:
 *   some_foo: The default value of this as long as does not exist
 *
 * services:
 *   AnRepository: ~
 *
 *   SomeSeoStuff:
 *     arguments:
 *       - AnRepository
 *       - '%some_foo%'
 *     add_action: template_redirect
 *
 *   RejectUnauthorizedThings:
 *     add_action:
 *       template_redirect:
 *         69: __invoke
 * ```
 *
 * Step after step this does:
 *
 * 1. "Register" a "some_foo"-option with a default value.
 * 2. Register the "AnRepository"-service
 * 3. Register the "SomeSeoStuff"-service
 *    * Use the "AnRepository"-service as first `__constructor` argument.
 *    * Use the "some_foo"-option as second argument.
 *    * Binds `SomeSeoStuff::__invoke` to the action "template_redirect"
 * 4. Register the "RejectUnauthorizedThings"-service
 *    * Binds `RejectUnauthorizedThings::__invoke`
 *      to the action "template_redirect"
 *      with priority 69.
 *
 * So in case the `template_redirect` action is fired the services
 * "RejectUnauthorizedThings" and "SomeSeoStuff" will be lazy loaded
 * and invoked.
 *
 *   This is the very short syntax.
 * Read on to know more about the complete syntax
 * and other possibilities like ...
 *
 * * `WordPress\Filter` to register filter,
 * * `WordPress\PostTypes` to register post-types,
 * * `WordPress\CliCommands` to add services as command in wp-cli
 *
 * ... and more.
 *
 * We suggest separating the definition into it's own file(s)
 * and load it into the (Pimple-)Container afterwards:
 *
 * ```php
 * <?php
 *
 * $container = new \Pimple\Container();
 *
 * $provider = new \RmpUp\WpDi\Provider();
 * $provider(require 'my-plugin-services.php', $container)
 * ```
 *
 * @copyright 2020 Mike Pretzlaw (https://mike-pretzlawWordPress.de)
 */
class ConfigTest extends AbstractTestCase
{
    private $definition;

    protected function setUp()
    {
        remove_all_actions('template_redirect'); // TODO use ::truncateActions instead as soon as available

        $this->definition = [
            Services::class => [
                ArrayObject::class,
                Mirror::class => [
                    'arguments' => ['foo'],
                ]
            ],
        ];

        parent::setUp();

        $provider = new Provider();
        $provider($this->definition, $this->pimple);
    }

    public function testServicesRegistered()
    {
        static::assertInstanceOf(ArrayObject::class, $this->container->get(ArrayObject::class));

        /** @var Mirror $mirror */
        $mirror = $this->container->get(Mirror::class);
        static::assertInstanceOf(Mirror::class, $mirror);

        static::assertEquals(['foo'], $mirror->getConstructorArgs());
    }

    /**
     * Before 0.8 an InvalidArgumentException has been thrown
     * when there is an unknown section in the definition.
     *
     * @internal
     */
    public function testThrowsNoExceptionWhenProviderInvalid()
    {
        $provider = new Provider();
        static::assertNull($provider([uniqid('', true) => []], $this->pimple));
    }
}