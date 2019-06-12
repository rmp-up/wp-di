<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ExtendsServiceReferencesTest.php
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
 * @since      2019-04-27
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Actions\Definition;

use RmpUp\WpDi\Provider\WordPress\Actions as Provider;
use RmpUp\WpDi\Sanitizer\WordPress\Actions;
use RmpUp\WpDi\Test\Mirror;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * Direct service definition at once
 *
 * One way to bind a service to the action is
 * to directly define it within the actions part:
 *
 * ```php
 * <?php
 *
 * use \RmpUp\WpDi\Provider\WordPress;
 *
 * return [
 *   WordPress\Actions::class => [
 *     'plugin_loaded' => [
 *       Foo::class,
 *     ]
 *   ]
 * ];
 * ```
 *
 * This does not only create a service named "Foo"
 * but also registers it (via `add_action`)
 * to watch for the "plugin_loaded"-action.
 * All arguments (just like the one for "plugin_loaded")
 * will be passed to the service like `$service( $plugin_name )` in this example.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-04-27
 */
class ServiceDefinitionTest extends SanitizerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->sanitizer = new Actions();
    }

    public function testExtendsClassNameToAction()
    {
        static::assertEquals(
            [
                'foo' => [
                    [
	                    Provider::SERVICE   => [
                            Mirror::class => [
	                            Provider::CLASS_NAME => Mirror::class,
	                            Provider::ARGUMENTS  => [],
                            ]
                        ],
	                    Provider::PRIORITY  => 10,
	                    Provider::ARG_COUNT => 1,
                    ]
                ]
            ],
            $this->sanitizer->sanitize([
                'foo' => [
                    Mirror::class
                ]
            ])
        );
    }
}