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
 * https://mike-pretzlaw.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@mike-pretzlaw.de so we can mail you a copy.
 *
 * @package    wp-di
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-di
 * @since      2019-05-29
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\PostTypes\Provider;

use PHPUnit\Framework\Constraint\IsEqual;
use Pretzlaw\WPInt\Filter\FilterAssertions;
use RmpUp\WpDi\Helper\WordPress\RegisterPostType;
use RmpUp\WpDi\Sanitizer\WpPostTypes;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * The post-type definition class
 *
 * Another option is to have an `__invoke()` method which takes care of the registration.
 * In that case the complete registration process will be delegated to this callable:
 *
 * ```php
 * <?php
 *
 * class PostTypeDefinition {
 *
 *   public function __invoke(string $postType) {
 *
 *     register_post_type(
 *       $postType,
 *       [
 *         'label' => 'Foo',
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
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-05-29
 */
class PostTypeDefinitionAsCallableTest extends AbstractTestCase
{
    use FilterAssertions;

    /**
     * @var \RmpUp\WpDi\Provider\WpPostTypes
     */
    private $provider;
    private static $called = false;

    protected function setUp()
    {
        parent::setUp();

        $sanitizer = new WpPostTypes();
        $this->provider = new \RmpUp\WpDi\Provider\WpPostTypes(
            $sanitizer->sanitize(
                [
                    'callable_type' => PostTypeDefinitionAsCallableTest::class,
                ]
            )
        );
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

        $this->pimple->register($this->provider);

        static::assertFilterHasCallback(
            'init',
            new IsEqual(new RegisterPostType($this->container, 'callable_type', 'callable_type'))
        );
    }
}