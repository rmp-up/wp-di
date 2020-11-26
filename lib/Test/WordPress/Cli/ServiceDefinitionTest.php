<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ServiceDefinitionTest.php
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

namespace RmpUp\WpDi\Test\WordPress\Cli;

use MyOwnCliCommand;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\WordPress\CliTestCase;
use WP_CLI;

/**
 * WP-CLI commands in service definition
 *
 * CLI commands can be defined while defining the service:
 *
 * ```yaml
 * services:
 *   MyOwnCliCommand:
 *     wp_cli: hello neighbour
 * ```
 *
 * This is the shortest form which will add the command "hello neighbour" in WP-CLI.
 * WP-CLI itself should delegate the execution to `MyOwnCliCommand::__invoke` as usual.
 *
 * Allowing one class to handle multiple commands can be defined like this:
 *
 * ```yaml
 * services:
 *   MyOwnCliCommand:
 *     arguments:
 *       - 3.14159
 *       - 'Hello World!'
 *     wp_cli:
 *       hello neighbour: ~
 *       hello world: terra
 *       hello upstairs: __invoke
 * ```
 *
 * In this example the service/class `MyOwnCliCommand` handles three CLI commands:
 *
 * 1. `wp hello neighbour`
 * 2. `wp hello world`
 * 3. `wp hello upstairs`
 *
 * The first one will execute the method `MyOwnCliCommand::__invoke()`
 * and the second one will make use of `MyOwnCliCommand::world()`.
 * With that it is possible to define multiple CLI commands in one class
 * or create aliases just like "hello upstairs" which is not different from
 * "hello neighbour" as it points to the same method
 * (`MyOwnCliCommand::__invoke()`).
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class ServiceDefinitionTest extends CliTestCase
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * @param $arguments
     * @param $name
     */
    private function assertCommandRegistered($name, $method, $arguments)
    {
        static::assertEquals($name, $arguments[0]);
        static::assertInternalType('array', $arguments[1]);
        static::assertLazyService('MyOwnCliCommand', $arguments[1][0]);
        static::assertSame($method, $arguments[1][1]);
    }

    public function getLongCliCommandSyntax()
    {
        return [
            '0.7' => [
                $this->yaml(1)
            ],
        ];
    }

    public function getShortCliCommandSyntax()
    {
        return [
            '0.7' => [
                $this->yaml(0)
            ]
        ];
    }

    /**
     * @dataProvider getShortCliCommandSyntax
     */
    public function testShortCliCommandSyntax($config)
    {
        $this->provider = new Provider();
        ($this->provider)($config, $this->pimple);

        static::assertEmpty(MyOwnCliCommand::_history());
        WP_CLI::run_command(['hello', 'neighbour']);

        static::assertCount(1, MyOwnCliCommand::_history('__invoke'));
    }

    /**
     * @param array $config
     * @dataProvider getLongCliCommandSyntax
     */
    public function testLongCliCommandSyntax($config)
    {
        $this->provider = new Provider();
        ($this->provider)($config, $this->pimple);

        $this->assertCliHasCommand('hello neighbour');
        $this->assertCliHasCommand('hello world');
        $this->assertCliHasCommand('hello upstairs');
    }
}