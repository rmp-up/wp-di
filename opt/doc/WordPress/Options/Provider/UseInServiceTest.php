<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * UseInServiceTest.php
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
 * @copyright 2021 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Options\Provider;

use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\Mirror;
use RmpUp\WpDi\Test\WordPress\Options\OptionsTestCase;
use SomeThing;

/**
 * Using options in services
 *
 * Options can be used like parameters:
 *
 * ```yaml
 * options:
 *   blog_public: ~
 *   ping_sites: ~
 *   my_own_new_one: 'Hello, hello, hello, how low'
 *
 * services:
 *   SomeThing:
 *     arguments:
 *       - '%blog_public%'
 *       - '%ping_sites%'
 *       - '%my_own_new_one%'
 * ```
 *
 * So we injected the options via constructor
 * instead of writing `get_option( 'ping_sites' )` somewhere in the class body.
 * Now it can make use of `$this->pingSites` which is easier to test and maintain.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class UseInServiceTest extends OptionsTestCase
{
    private $customOptionValue = 'Hello, hello, hello, how low';

    protected function compatSetUp()
    {
        parent::compatSetUp();

        $this->registerServices();

        $this->mockFilter('pre_option_blog_public')
			->andReturn(0);
        $this->mockFilter('pre_option_ping_sites')
			->andReturn(['example.org', 'rmp-up.de']);
    }

    public function testInjectOptions()
    {
        /** @var Mirror $mirror */
        $mirror = $this->pimple[SomeThing::class];

        static::assertEquals(
            [0, ['example.org', 'rmp-up.de'], $this->customOptionValue],
            $mirror->getConstructorArgs()
        );
    }

    public function testInjectExistingOptionsInsteadOfDefault()
    {
        $this->mockFilter('pre_option_my_own_new_one')
			->once()
			->andReturn('Ta Hun Kwai');

        /** @var Mirror $mirror */
        $mirror = $this->pimple[SomeThing::class];

        static::assertEquals('Ta Hun Kwai', $mirror->getConstructorArgs()[2]);
    }

    public function compatTearDown()
    {
        remove_all_filters('pre_option_blog_public');
        remove_all_filters('pre_option_ping_sites');
        remove_all_filters('pre_option_with_default');

        parent::compatTearDown();
    }
}
