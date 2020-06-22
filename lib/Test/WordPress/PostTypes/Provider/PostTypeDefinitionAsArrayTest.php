<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PostTypeDefinitionAsArray.php
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

namespace RmpUp\WpDi\Test\WordPress\PostTypes\Provider;

use PHPUnit\Framework\Constraint\IsEqual;
use Pretzlaw\WPInt\Filter\FilterAssertions;
use RmpUp\WpDi\Helper\WordPress\RegisterPostType;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Provider\WordPress\PostTypes as PostTypesProvider;
use RmpUp\WpDi\Sanitizer\WordPress\PostTypes;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * The post-type definition class
 *
 * When then definition class can be converted it into an array
 * then its fields will be used for post type registration.
 * Such class should contain the config as public fields:
 *
 * ```php
 * <?php
 *
 * class TypeBox {
 *   public $label = 'Heart Shaped Objects';
 *   public $public = false;
 * }
 * ```
 *
 * In this case the post-type won't be public
 * but occur using the label "Heart Shaped Objects"
 * if registered using ...
 *
 * ```yaml
 * services:
 *   TypeBox:
 *     post_type: albini
 * ```
 *
 * @copyright 2020 Mike Pretzlaw (https://rmp-up.de)
 */
class PostTypeDefinitionAsArrayTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('TypeBox')) {
            $this->classComment()->execute(0);
        }

        remove_all_actions('init');
    }

    protected function tearDown()
    {
        if (post_type_exists('albini')) {
            static::assertTrue(unregister_post_type('albini'));
        }

        parent::tearDown();
    }

    public function testServiceConvertedToArray()
    {
        $this->pimple->register(new Provider($this->yaml(0)));

        static::assertFilterHasCallback(
            'init',
            new IsEqual(new RegisterPostType($this->pimple, 'TypeBox', 'albini'))
        );
    }

    public function testPostTypeIsRegistered()
    {
        static::assertFalse(post_type_exists('albini'));
        $this->pimple->register(new Provider($this->yaml(0)));
        do_action('init');
        static::assertTrue(post_type_exists('albini'));
    }
}