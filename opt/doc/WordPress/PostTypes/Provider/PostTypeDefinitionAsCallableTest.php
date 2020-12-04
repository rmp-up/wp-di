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
use RmpUp\WpDi\Factory;
use RmpUp\WpDi\Helper\WordPress\RegisterPostType;
use RmpUp\WpDi\Sanitizer\WordPress\PostTypes;
use RmpUp\WpDi\Test\AbstractTestCase;
use function RmpUp\WpDi\Factory;

/**
 * The post-type definition class
 *
 * Another option is to have an `__invoke()` method which takes care of the registration.
 * In that case the complete registration process will be delegated to this callable:
 *
 * ```php
 * <?php
 *
 * class TypeSappy {
 *
 *   public function __invoke(string $postType) {
 *
 *     register_post_type(
 *       $postType,
 *       [
 *         'label' => 'Chords',
 *         'public' => false,
 *       ]
 *     );
 *
 *  }
 * }
 * ```
 *
 * As you can see the name of the post type will be provided
 * to let the object know which one shall be registered.
 * Using this class in the known syntax ...
 *
 * ```yaml
 * services:
 *   TypeSappy:
 *     post_type: daga_afd
 * ```
 *
 * makes wp-di use the custom `__invoke` instead of `register_post_type`.
 *
 * @copyright 2020 Mike Pretzlaw (https://rmp-up.de)
 */
class PostTypeDefinitionAsCallableTest extends AbstractTestCase
{
    private static $called = false;

    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('TypeSappy')) {
            $this->classComment()->execute(0);
        }

        remove_all_actions('init');
    }

    public function __invoke($postType)
    {
        static::$called = true;

        register_post_type(
            $postType,
            [
                'public' => true,
                'description' => 'Wuseldusel',
                'capability_type' => 'elephant'
            ]
        );
    }

    public function testServiceExecuted()
    {
        static::assertEmpty(static::$calls);

        (Factory::createProvider())(
            [
                'services' => [
                    'callable_type' => [
                        'class' => get_class($this),
                        'post_type' => 'callable_type',
                    ],
                ]
            ],
            $this->pimple
        );

        static::assertFilterHasCallback(
            'init',
            new IsEqual(new RegisterPostType($this->pimple, 'callable_type', 'callable_type'))
        );
    }

    public function testPostTypeIsRegistered()
    {
        static::assertFalse(post_type_exists('daga_afd'));
        $this->registerServices();
        do_action('init');
        static::assertTrue(post_type_exists('daga_afd'));
    }
}