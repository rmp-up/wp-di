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

use RmpUp\WpDi\Provider\WpActions;
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
 * use \RmpUp\WpDi\Provider;
 *
 * return [
 *   Provider\WpActions => [
 *     'wp_ajax_crop_image_pre_save' => [
 *       [
 *         WpActions::SERVICE => Mirror::class,
 *         WpActions::PRIORITY => 5,
 *         WpActions::ARG_COUNT => 3,
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
class RegistersActionTest extends AbstractWpActionsTest
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|WpActions
     */
    private $provider;

    protected function setUp()
    {
        parent::setUp();

        $this->pimple[Mirror::class] = new Mirror();

        $services = $this->sanitizer->sanitize([
            'foo_event' => [
                [
                    WpActions::SERVICE => Mirror::class,
                    WpActions::PRIORITY => 1337,
                    WpActions::ARG_COUNT => 42,
                ],
            ]
        ]);

        $this->provider = $this->getMockBuilder(WpActions::class)
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
}