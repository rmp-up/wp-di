<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MappingMethodsTest.php
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
use RmpUp\WpDi\Test\WordPress\CliTestCase;

/**
 * Multiple commands in one class
 *
 * When we want to make one class responsible for multiple commands
 * or need to support some backward compatibility,
 * then we can map multiple commands to public methods like this:
 *
 * ```yaml
 * services:
 *   MyOwnCliCommand:
 *     arguments:
 *       - 'Take me to your landlord!'
 *       - 'I have seen a mice in the nature!'
 *     wp_cli:
 *       trolley go: doGo
 *       trolley run: doGo
 *       trolley stop: doStop
 *       trolley nag: doNag
 * ```
 *
 * The class may look like this then:
 *
 * ```php
 * <?php
 *
 * class MyOwnCliCommand {
 *     public function doGo() { ... };
 *     public function doStop() { ... };
 *     public function doNag() { ... };
 * }
 * ```
 *
 * So when we now open the CLI and run
 *
 * ```
 * wp trolley nag
 * ```
 *
 * Then wp-di will lazy load the MyOwnCliCommand-Service
 * and make wp-cli run the Method "MyOwnCliCommand::doNag".
 *
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class MappingMethodsTest extends CliTestCase
{
    public function testCommandCanBeMappedToMethod()
    {
        $this->assertCliNotHasCommand('trolley run');

        $this->registerServices();

        $this->assertCliHasCommand('trolley run');
    }

    /**
     * Arguments are mandatory in this test.
     * When we provide no arguments then the class-name will be forwarded to wp-cli
     * which works fine most of the time.
     * But with arguments we put the LazyService proxy in between which causes some trouble
     * as wp-cli considers this to be the final class and does reflection on it.
     * As the proxy does not look the same (does not have the target methods)
     * wp-cli does stop and fails to execute.
     * Therefore we need arguments in the example.
     *
     * @internal
     */
    public function testHasArguments()
    {
        static::assertArrayHasKey(
            'arguments', $this->yaml(0, 'services', 'MyOwnCliCommand'),
            'This test needs arguments in the example because with arguments there is a higher chance of bugs.'
        );
    }

    public function testInvokingMappedCommandDoesNotFail()
    {
        $this->registerServices();

        static::assertEmpty(MyOwnCliCommand::_history());
        \WP_CLI::run_command(['trolley', 'nag']);

        static::assertNotEmpty(MyOwnCliCommand::_history('doNag'), 'Method ::doNag has not been called');
        static::assertCount(1, MyOwnCliCommand::_history('doNag'), 'Method ::doNag has been called more than once');
        static::assertEquals(
            [[], []],
            MyOwnCliCommand::_history('doNag')[0]['arguments'],
            'Method ::doNag been called with the wrong arguments'
        );
    }
}