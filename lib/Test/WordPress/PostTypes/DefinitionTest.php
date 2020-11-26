<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DefinitionTest.php
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

namespace RmpUp\WpDi\Test\WordPress\PostTypes;

use PHPUnit\Framework\Constraint\IsInstanceOf;
use RmpUp\WpDi\Helper\WordPress\RegisterPostType;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * Defining a post-type service
 *
 * Post-types can become services too which is very handy in some situations.
 * First you need to add the "post_type" keyword to register a new post-type:
 *
 * ```yaml
 * services:
 *   MyOwnPostType:
 *     post_type: animals
 * ```
 *
 * With this we have registered the post-type "animals".
 * An instance of the `MyOwnPostType` class will be converted into an array
 * which defines the post-type as known from `register_post_type()`.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class DefinitionTest extends AbstractTestCase
{
    private $backupPostTypes;

    protected function setUp()
    {
        parent::setUp();

        global $wp_post_types;
        $this->backupPostTypes = $wp_post_types;

        remove_all_actions('init');
    }

    protected function tearDown()
    {
        global $wp_post_types;
        $wp_post_types = $this->backupPostTypes;

        parent::tearDown();
    }

    public function testSimplePostTypeExample()
    {
        $this->assertFilterNotHasCallback('init', new IsInstanceOf(RegisterPostType::class));

        $this->registerServices();

        $this->assertFilterHasCallback('init', new IsInstanceOf(RegisterPostType::class));

        $initHook = $this->getFilter('init')->callbacks[10];
        $recent = end($initHook);

        static::assertInstanceOf(RegisterPostType::class, $recent['function']);
        static::assertSame('animals', $recent['function']->getPostType());
    }
}