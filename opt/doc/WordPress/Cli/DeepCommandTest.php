<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DeepCommandTest.php
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
 * @copyright 2021 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Cli;

use RmpUp\WpDi\Test\WordPress\CliTestCase;

/**
 * Deep commands
 *
 * Deep commands are what we call commands
 * that go very deep in the tree of commands without actually providing
 * some functionality in the parent nodes.
 *
 * Example:
 *
 * ```yaml
 * services:
 *   MyOwnCliCommand:
 *     wp_cli: my own cli command
 * ```
 *
 * This shall register the command `wp my own cli command`
 * while the upper/parent commands `wp my`, `wp my own`
 * and `wp my own cli` do nothing at all.
 * Those will be added as a placeholder allowing other commands among them:
 *
 * ```yaml
 * services:
 *   MyOwnCliCommand:
 *     wp_cli: my own cli command
 *   SomeThing:
 *     arguments:
 *       - 'TLL&DSoMEC'
 *     wp_cli: my own thing
 * ```
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class DeepCommandTest extends CliTestCase
{
    public function testParentBecomePlaceholder()
    {
        $this->assertCliNotHasCommand('my own cli command');

        $this->registerServices();

        $this->assertCliHasCommand('my own cli command');
    }

    public function testParentNodesCanBeReused()
    {
        $this->assertCliNotHasCommand('my own cli command');
        $this->assertCliNotHasCommand('my own thing');

        $this->registerServices(1);

        $this->assertCliHasCommand('my own cli command');
        $this->assertCliHasCommand('my own thing');
    }
}