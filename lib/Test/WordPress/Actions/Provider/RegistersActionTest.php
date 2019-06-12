<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ClosuresTest.php
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
 * @since      2019-05-28
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Actions;

use PHPUnit\Framework\MockObject\MockObject;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Provider\WordPress\Actions;
use RmpUp\WpDi\Test\Mirror;

/**
 * Priority and arguments
 *
 * There is often the need to only adjust the priority
 * or the argument count.
 * This is how it can be done:
 *
 * ```php
 * <?php
 *
 * use \RmpUp\WpDi\Provider\WordPress;
 *
 * return [
 *   WordPress\Actions::class => [
 *     'wp_ajax_crop_image_pre_save' => [
 *       [
 *         WordPress\Actions::SERVICE => Mirror::class,
 *         WordPress\Actions::PRIORITY => 5,
 *         WordPress\Actions::ARG_COUNT => 3,
 *       ]
 *     ]
 *   ]
 * ];
 * ```
 *
 * This registers the service using priority 5
 * and allows all 3 arguments to be passed to the service.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-05-28
 */
class RegistersActionTest extends AbstractActionsTest
{
    /**
     * @var MockObject|Actions
     */
    private $provider;

    protected function setUp()
    {
        parent::setUp();

        $this->pimple[Mirror::class] = new Mirror();

        $services = $this->sanitizer->sanitize([
            'foo_event' => [
                [
                    Actions::SERVICE => Mirror::class,
                    Actions::PRIORITY => 1337,
                    Actions::ARG_COUNT => 42,
                ],
            ]
        ]);

        $this->provider = $this->getMockBuilder(Actions::class)
            ->setConstructorArgs([$services])
            ->setMethods(['registerAction'])
            ->enableProxyingToOriginalMethods()
            ->getMock();
    }

    public function testClosureIsRegistered()
    {
        $this->provider->expects($this->once())
            ->method('registerAction')
            ->with(
                'foo_event',
                $this->container,
                Mirror::class,
                1337,
                42
            );

        $this->provider->register($this->pimple);
    }

    public function testClosureDefinition()
    {
        $this->pimple->register(
            new Provider(
                [
                    Actions::class => [
                        'myOwnSomething' => [
                            'okydoky' => static function ($container) {
                                return $container;
                            }
                        ]
                    ]
                ]
            )
        );

        static::assertEquals($this->pimple, $this->pimple['okydoky']);
    }

    public function testRegisterReference()
    {
        $this->pimple->register(
            new Provider(
                [
                    Provider\Services::class => [
                        'okydoky' => static function () {
                            return static function () {
                                return 1337.42;
                            };
                        }
                    ],

                    Actions::class => [
                        'myOwnSomething' => [
                            'okydoky',
                        ]
                    ]
                ]
            )
        );

        static::assertSame(1337.42, apply_filters('myOwnSomething', 0.0));
    }

    protected function tearDown()
    {
        remove_all_filters('myOwnSomething');
        parent::tearDown();
    }
}