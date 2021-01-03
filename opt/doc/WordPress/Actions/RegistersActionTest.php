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

namespace RmpUp\WpDi\Test\WordPress\Actions;

use MyOwnFilterHandler;
use PHPUnit\Framework\Constraint\IsEqual;
use RmpUp\WpDi\Helper\LazyPimple;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * Priority and arguments
 *
 * The priority of a action- or filter-hook is done by going one level deeper
 * and mapping the priority to the class-method (default: __invoke).
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
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class RegistersActionTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        remove_all_filters('posts_pre_query');
        remove_all_filters('template_redirect');

        $this->registerServices(0);
        $this->registerServices(1);
    }

    public function testPriorityToOneMethod()
    {
        static::assertFilterHasCallback(
            'template_redirect',
            new IsEqual(new LazyPimple($this->pimple, MyOwnFilterHandler::class)),
            1
        );
    }

    public function testMultiplePriorityToMultipleMethods()
    {
        static::assertFilterHasCallback(
            'posts_pre_query',
            new IsEqual(
                [new LazyPimple($this->pimple, MyOwnFilterHandler::class), 'warmUpCache']
            ),
            4
        );

        static::assertFilterHasCallback(
            'posts_pre_query',
            new IsEqual(
                [new LazyPimple($this->pimple, MyOwnFilterHandler::class), 'applyCache']
            ),
            4
        );

        static::assertFilterHasCallback(
            'posts_pre_query',
            new IsEqual(
                [new LazyPimple($this->pimple, MyOwnFilterHandler::class), 'writeCache']
            ),
            13
        );
    }

    protected function tearDown()
    {
        remove_all_filters('posts_pre_query');
        remove_all_filters('template_redirect');

        parent::tearDown();
    }
}