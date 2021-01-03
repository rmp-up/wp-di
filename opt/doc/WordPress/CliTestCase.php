<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * CliTestCase.php
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

namespace RmpUp\WpDi\Test\WordPress;

use MyOwnCliCommand;
use RmpUp\WpDi\Test\AbstractTestCase;
use WP_CLI;

/**
 * CliTestCase
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
abstract class CliTestCase extends AbstractTestCase
{
    public function assertCliHasCommand($command)
    {
        $cliCommand = $this->findCliCommand($command);
        static::assertTrue(is_array($cliCommand));
    }

    public function assertCliNotHasCommand($command)
    {
        static::assertTrue(is_string($this->findCliCommand($command)));
    }

    protected function findCliCommand($command)
    {
        // If command is input as a string, then explode it into array.
        if (is_string($command)) {
            $command = explode(' ', $command);
        }

        return \WP_CLI::get_runner()->find_command_to_run($command);
    }

    protected function tearDown()
    {
        MyOwnCliCommand::_reset();

        // Truncate wp-cli
        $rootCommand = WP_CLI::get_root_command();
        foreach ($rootCommand->get_subcommands() as $cmd) {
            if ($cmd instanceof WP_CLI\Dispatcher\CompositeCommand) {
                $cmd = $cmd->get_name();
            }

            $rootCommand->remove_subcommand($cmd);
        }

        parent::tearDown();
    }
}