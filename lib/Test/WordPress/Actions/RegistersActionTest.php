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

use MyOwnFilterHandler;
use PHPUnit\Framework\Constraint\IsEqual;
use RmpUp\WpDi\LazyService;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * Priority and arguments
 *
 * There is often the need to only adjust the priority
 * or the argument count.
 * This is how it can be done:
 *
 * ```yaml
 * services:
 *   MyOwnFilterHandler:
 *     add_filter:
 *       template_redirect:
 *         1: ~
 * ```
 *
 * This will set the priority to 1.
 *
 * Now let's say you have a class like this:
 *
 * ```text
 * class MyOwnFilterHandler
 *   function warmUpCache
 *   function applyCache
 *   function writeCache
 * ```
 *
 * And need to hook before and after a filter or action.
 * This would need some more details:
 *
 * ```yaml
 * services:
 *   MyOwnFilterHandler:
 *     add_filter:
 *       posts_pre_query:
 *         4: [ warmUpCache, applyCache ]
 *         13: writeCache
 * ```
 *
 * For the filter "posts_pre_query" this would ...
 *
 * * register `MyOwnFilterHandler::warmUpCache` on priority 4
 * * register `MyOwnFilterHandler::applyCache` on priority 4
 * * register `MyOwnFilterHandler::writeCache` on priority 13
 *
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 */
class RegistersActionTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        remove_all_filters('posts_pre_query');
        remove_all_filters('template_redirect');

        $this->pimple->register(new Provider($this->yaml(0)));
        $this->pimple->register(new Provider($this->yaml(1)));
    }

    public function testPriorityToOneMethod()
    {
        static::assertFilterHasCallback(
            'template_redirect',
            new IsEqual(
                [new LazyService($this->container, MyOwnFilterHandler::class), '__invoke']
            )
        );
    }

    public function testMultiplePriorityToMultipleMethods()
    {
        static::assertFilterHasCallback(
            'posts_pre_query',
            new IsEqual(
                [new LazyService($this->container, MyOwnFilterHandler::class), 'warmUpCache']
            )
        );

        static::assertFilterHasCallback(
            'posts_pre_query',
            new IsEqual(
                [new LazyService($this->container, MyOwnFilterHandler::class), 'applyCache']
            )
        );

        static::assertFilterHasCallback(
            'posts_pre_query',
            new IsEqual(
                [new LazyService($this->container, MyOwnFilterHandler::class), 'writeCache']
            )
        );

        // This needs to be covered by rmp-up/wp-integration-test
        $this->markTestIncomplete('Did not check for the correct priority.');
    }

    protected function tearDown()
    {
        remove_all_filters('posts_pre_query');
        remove_all_filters('template_redirect');

        parent::tearDown();
    }
}