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

use MyOwnActionListener;
use MyOwnFilterHandler;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Provider\WordPress\Actions as Provider;
use RmpUp\WpDi\Sanitizer\WordPress\Actions;
use RmpUp\WpDi\Test\Mirror;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * Direct service definition at once
 *
 * To bind a service to an action or filter can be done using common keywords:
 *
 * ```yaml
 * services:
 *   MyOwnFilterHandler:
 *     add_filter: the_content
 * ```
 *
 * Which is already the shortest possible definition.
 * This way an instance of the `MyOwnFilterHandler` class
 * will be registered as a service.
 * When the "the_content" filter runs in WordPress
 * then it would call the `MyOwnFilterHandler::__invoke` method of the object.
 *
 * When the service/object is needed for multiple filter/actions
 * then we go in detail after "add_action"/"add_filter" like this:
 *
 * ```yaml
 * services:
 *   MyOwnActionListener:
 *     add_action:
 *       personal_options_update: ~
 *       edit_user_profile_update: ~
 *       user_edit_form_tag: editFormTag
 * ```
 *
 * Now the two actions "personal_options_update"
 * and "edit_user_profile_update" would both run the
 * `MyOwnActionListener::__invoke` method.
 * But the third action ("user_edit_form_tag") would register
 * `MyOwnActionListener::editFormTag` (with the default priority of 10).
 *
 * Internally the above configs will be extended to a more complete config
 * which also allows to change the priority:
 *
 * ```yaml
 * services:
 *   MyOwnFilterHandler:
 *     filter:
 *      the_content:
 *        10: __invoke
 *
 *   MyOwnActionListener:
 *     action:
 *       personal_options_update:
 *         10: __invoke
 *       edit_user_profile_update:
 *         42: someOtherMethod
 * ```
 *
 * Writing such a complete definition allows to change the priority.
 * Besides the keywords "action"
 * and "filter" are valid aliases for "add_action"
 * and "add_filter".
 *
 * Note: The above examples do not need to tell how many arguments are needed.
 * The DI will take care of this as it just passes any given argument
 * and leaves the correct handling to PHP.
 *
 * A more deprecated way to bind a service to the action is
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
    private function assertLazyFilterRegistered($filterName, $serviceName, $method = '__invoke', $priority = 10)
    {
        self::assertActionNotEmpty($filterName);
        $filter = current($this->getFilter($filterName)->callbacks[$priority]);
        self::assertLazyService($serviceName, $filter['function'][0]);
        self::assertSame($method, $filter['function'][1]);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->sanitizer = new Actions();

        remove_all_filters('the_content');
        $this->assertActionEmpty('the_content');

        remove_all_actions('personal_options_update');
        $this->assertActionEmpty('personal_options_update');

        remove_all_actions('edit_user_profile_update');
        $this->assertActionEmpty('edit_user_profile_update');

        remove_all_actions('user_edit_form_tag');
        $this->assertActionEmpty('user_edit_form_tag');
    }

    public function testExtendsClassNameToAction()
    {
        static::assertEquals(
            [
                'foo' => [
                    [
                        Provider::SERVICE => [
                            Mirror::class => [
                                Provider::CLASS_NAME => Mirror::class,
                                Provider::ARGUMENTS => [],
                            ]
                        ],
                        Provider::PRIORITY => 10,
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

    public function testShortFilterDefinition()
    {
        $this->pimple->register(
            new \RmpUp\WpDi\Provider(
                [
                    Services::class => $this->yaml(0, 'services')
                ]
            )
        );

        $this->assertLazyFilterRegistered('the_content', MyOwnFilterHandler::class);
    }

    public function testMultipleFilterDefinition()
    {
        $this->pimple->register(
            new \RmpUp\WpDi\Provider(
                [
                    Services::class => $this->yaml(1, 'services')
                ]
            )
        );

        $this->assertLazyFilterRegistered('personal_options_update', MyOwnActionListener::class);
        $this->assertLazyFilterRegistered('edit_user_profile_update', MyOwnActionListener::class);
        $this->assertLazyFilterRegistered('user_edit_form_tag', MyOwnActionListener::class, 'editFormTag');
    }

    public function testCompleteFilterDefinition()
    {
        $this->pimple->register(
            new \RmpUp\WpDi\Provider(
                [
                    Services::class => $this->yaml(2, 'services')
                ]
            )
        );

        $this->assertLazyFilterRegistered('personal_options_update', MyOwnActionListener::class);
        $this->assertLazyFilterRegistered(
            'edit_user_profile_update',
            MyOwnActionListener::class,
            'someOtherMethod',
            42
        );
    }
}