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
 * https://mike-pretzlaw.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@mike-pretzlaw.de so we can mail you a copy.
 *
 * @package    wp-di
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-di
 * @since      2019-05-30
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test;

use ArrayObject;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use Pretzlaw\WPInt\Filter\FilterAssertions;
use RmpUp\WpDi\Helper\WordPress\RegisterPostType;
use RmpUp\WpDi\LazyService;
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
 * ```php
 * <?php // my-plugin-services.php
 *
 * use \RmpUp\WpDi\Provider;
 * use \RmpUp\WpDi\Provider\WordPress;
 *
 * return [
 *   Provider\Services::class => [
 *     AnRepository::class,
 *
 *     SomeSeoStuff::class => [
 *       AnRepository::class,
 *       'some_foo'
 *     ]
 *   ],
 *
 *   WordPress\Options::class => [
 *     'some_foo' => 'The default value of this as long as not given',
 *   ]
 *
 *   WordPress\Actions::class => [
 *     'template_redirect' => [
 *       RejectUnauthorizedThings::class,
 *       SomeSeoStuff::class,
 *     ]
 *   ]
 * ];
 * ```
 *
 * Step after step this does:
 *
 * 1. Register the "AnRepository"-service
 * 2. Register the "SomeSeoStuff"-service
 *    * Use the "AnRepository"-service as first `__constructor` argument.
 *    * Use the "some_foo"-option as second argument.
 * 3. "Register" a "some_foo"-option with a default value.
 * 4. Add services to the `template_redirect` action:
 *    * A new "RejectUnauthorizedThings"-service
 *    * The existing "SomeSeoStuff"-service
 *
 * So in case the `template_redirect` action is fired the services
 * "RejectUnauthorizedThings" and "SomeSeoStuff" will be lazy loaded
 * and invoked (e.g. using `__invoke`).
 *
 *   This is the very short syntax.
 * Read on to know more about the complete syntax
 * and other possibilities like ...
 *
 *
 * * `WordPress\Filter` to register filter,
 * * `WordPress\PostTypes` to register post-types,
 * * `WordPress\CliCommands` to add services as command in wp-cli
 *
 * ... and more.
 *
 * We suggest seperating the definition into it's own file(s)
 * and load it into the (Pimple-)Container afterwards:
 *
 * ```php
 * <?php
 *
 * $container = new \Pimple\Container();
 *
 * $container->register(
 *   new \RmpUp\WpDi\Provider( require 'my-plugin-services.php' )
 * );
 * ```
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlawWordPress.de)
 * @since      2019-05-30
 */
class AutoResolvingProviderTest extends AbstractTestCase
{
    use FilterAssertions;

    private $definition = [
        Services::class => [
            ArrayObject::class,
            Mirror::class => [
                'foo',
            ]
        ],

        Actions::class => [
            'template_redirect' => [
                Mirror::class,
            ]
        ],

        PostTypes::class => [
            'qux' => ArrayObject::class
        ]
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->pimple->register(
            new Provider($this->definition)
        );
    }

    public function testServicesRegistered()
    {
        static::assertInstanceOf(ArrayObject::class, $this->container->get(ArrayObject::class));

        /** @var Mirror $mirror */
        $mirror = $this->container->get(Mirror::class);
        static::assertInstanceOf(Mirror::class, $mirror);

        static::assertEquals(['foo'], $mirror->getConstructorArgs());
    }

    public function testActionsRegistered()
    {
        self::assertFilterHasCallback('template_redirect', new IsInstanceOf(LazyService::class));
        self::assertFilterHasCallback('template_redirect', new IsEqual(
            new LazyService($this->container, Mirror::class)
        ));
    }

    public function testPostTypeRegistered()
    {
        static::assertFilterHasCallback('init', new IsInstanceOf(RegisterPostType::class));
        static::assertFilterHasCallback('init', new IsEqual(new RegisterPostType($this->container, 'qux', 'qux')));
    }

    public function testThrowsExceptionWhenProviderInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->pimple->register(
            new Provider([uniqid('', true) => []])
        );
    }
}