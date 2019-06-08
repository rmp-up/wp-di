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
 * When then definition class can be converted it into an array
 * then its fields will be used for post type registration.
 * Such class should contain the config as public fields:
 *
 * ```php
 * <?php
 *
 * class PostTypeDefinition {
 *   public $label = 'Foo';
 *   public $public = false;
 * }
 * ```
 *
 * In this case the post-type won't be public but occur using the label "Foo".
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-05-29
 */
class PostTypeDefinitionAsArrayTest extends AbstractTestCase
{
    use FilterAssertions;

    public $public = false;
    public $description = 'Yayyyyy';
    public $capability_type = 'custom_stuff';
    /**
     * @var \RmpUp\WpDi\Provider\WpPostTypes
     */
    private $provider;

    protected function setUp()
    {
        parent::setUp();

        $sanitizer = new WpPostTypes();
        $this->provider = new \RmpUp\WpDi\Provider\WpPostTypes(
            $sanitizer->sanitize(
                [
                    'some_type' => PostTypeDefinitionAsArrayTest::class,
                ]
            )
        );
    }

    public function testServiceConvertedToArray()
    {
        static::assertEmpty(static::$calls);

        $this->pimple->register($this->provider);

        static::assertFilterHasCallback(
            'init',
            new IsEqual(new RegisterPostType($this->container, 'some_type', 'some_type'))
        );
    }
}