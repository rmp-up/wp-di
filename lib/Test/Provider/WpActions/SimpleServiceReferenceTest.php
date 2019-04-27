<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SimpleServiceReferenceTest.php
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
 * @since      2019-04-25
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\Provider\WpActions;

use RmpUp\WpDi\Test\Mirror;

/**
 * SimpleServiceReferenceTest
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-04-25
 */
class SimpleServiceReferenceTest extends AbstractWpActionsTest
{
    const SERVICE_NAME = 'butNamed';

    protected function setUp()
    {
        $this->services = [
            __CLASS__ => [
                Mirror::class,
                self::SERVICE_NAME => Mirror::class,
            ]
        ];

        parent::setUp();
    }

    public function testAddsService()
    {
        static::assertInstanceOf(Mirror::class, $this->container->get(Mirror::class));
        static::assertInstanceOf(Mirror::class, $this->container->get(self::SERVICE_NAME));
    }

    public function testAddsAction()
    {
        static::assertLazyService(Mirror::class, static::$actions[__CLASS__][0][0]);
        static::assertLazyService(self::SERVICE_NAME, static::$actions[__CLASS__][1][0]);
    }
}