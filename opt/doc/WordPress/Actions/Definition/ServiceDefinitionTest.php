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

namespace RmpUp\WpDi\Test\WordPress\Actions\Definition;

use MyOwnActionListener;
use MyOwnFilterHandler;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Test\AbstractTestCase;

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
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class ServiceDefinitionTest extends AbstractTestCase
{
    private function assertLazyFilterRegistered($filterName, $serviceName, $method = '__invoke', $priority = 10)
    {
        self::assertActionNotEmpty($filterName);
        $filter = current($this->getFilter($filterName)->callbacks[$priority]);
        self::assertLazyPimple($serviceName, $filter['function'][0]);
        self::assertSame($method, $filter['function'][1]);
    }

    public function getCompleteDefinition(): array
    {
        return [
            'v0.7' => [
                $this->yaml(2)
            ],
        ];
    }

    public function getMultipleFilterDefinition(): array
    {
        return [
            '0.6' => [
                [
                    Services::class => $this->yaml(1, 'services')
                ]
            ],
            '0.7' => [
                $this->yaml(1)
            ]
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        remove_all_filters('the_content');
        $this->assertActionEmpty('the_content');

        remove_all_actions('personal_options_update');
        $this->assertActionEmpty('personal_options_update');

        remove_all_actions('edit_user_profile_update');
        $this->assertActionEmpty('edit_user_profile_update');

        remove_all_actions('user_edit_form_tag');
        $this->assertActionEmpty('user_edit_form_tag');
    }

    public function testShortFilterDefinition()
    {
        (new \RmpUp\WpDi\Provider())(
            [
                Services::class => $this->yaml(0, 'services')
            ],
            $this->pimple
        );

        $this->assertLazyFilterRegistered('the_content', MyOwnFilterHandler::class);
    }

    /**
     * @dataProvider getMultipleFilterDefinition
     */
    public function testMultipleFilterDefinition($config)
    {
        (new \RmpUp\WpDi\Provider())($config, $this->pimple);

        $this->assertLazyFilterRegistered('personal_options_update', MyOwnActionListener::class);
        $this->assertLazyFilterRegistered('edit_user_profile_update', MyOwnActionListener::class);
        $this->assertLazyFilterRegistered('user_edit_form_tag', MyOwnActionListener::class, 'editFormTag');
    }

    /**
     * @dataProvider getCompleteDefinition
     */
    public function testCompleteFilterDefinition($definition)
    {
        (new \RmpUp\WpDi\Provider())($definition, $this->pimple);

        $this->assertLazyFilterRegistered('personal_options_update', MyOwnActionListener::class);
        $this->assertLazyFilterRegistered(
            'edit_user_profile_update',
            MyOwnActionListener::class,
            'someOtherMethod',
            42
        );
    }
}