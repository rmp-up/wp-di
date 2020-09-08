<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpCli.php
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

namespace RmpUp\WpDi\Compiler;

use Pimple\Container;
use RmpUp\WpDi\Helper\InvokeRedirect;
use RmpUp\WpDi\Helper\LazyPimple;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Provider\WordPress\CliCommands;
use WP_CLI;
use WP_CLI\Dispatcher\CompositeCommand;

/**
 * WpCli
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class WpCli implements CompilerInterface
{
    /**
     * @var string
     */
    private $wpCliClass;

    public function __construct($wpCliClass = null)
    {
        if (null === $wpCliClass) {
            $wpCliClass = '\\WP_CLI';
        }

        $this->wpCliClass = $wpCliClass;
    }

    public function __invoke($commandToMethod, string $serviceName, Container $pimple)
    {
        if ('cli' !== PHP_SAPI || false === class_exists($this->wpCliClass)) {
            // Only register CLI commands when CLI is active and WP_CLI is available.
            return;
        }

        if (is_scalar($commandToMethod)) {
            $commandToMethod = [$commandToMethod => null];
        }

        foreach ($commandToMethod as $command => $method) {
            if (null === $method) {
                $method = '__invoke';
            }

            $serviceDefinition = $pimple->raw($serviceName);

            if (empty($serviceDefinition[Services::ARGUMENTS])) {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->addCommand((string) $command, $serviceDefinition[Services::CLASS_NAME], $method);
                continue;
            }

            if ($pimple->offsetExists($serviceName)) {
                // Command is wired to existing service.
                /** @noinspection PhpUndefinedMethodInspection */
                $this->addCommand((string) $command, new LazyPimple($pimple, $serviceName), $method);
                continue;
            }
        }
    }

    /**
     * @param string $command
     * @param object $handler
     * @param string $method
     */
    private function addCommand(string $command, $handler, string $method)
    {
        $this->assertPathToCommand($command);

        if ('__invoke' !== $method) {
            // wp-cli uses reflection on the method
            // which is not given when $handler is a proxy already
            // so we give wp-cli the __invoke method and redirect later on.
            $handler = new InvokeRedirect([$handler, $method]);
        }

        ($this->wpCliClass)::add_command($command, $handler);
    }

    private function assertPathToCommand(string $command)
    {
        $parent = WP_CLI::get_root_command();
        $class = $this->wpCliClass;
        $path = (array) explode(' ', $command);
        array_pop($path);

        if (empty($path)) {
            // No path left to assert
            return;
        }

        $currentScope = [];
        foreach ($path as $node) {
            $currentScope[] = $node;
            $currentNamespace = new CompositeCommand($parent, $node, new \WP_CLI\DocParser(''));
            $parent = $currentNamespace;

            $definition = $class::get_runner()->find_command_to_run($currentScope);
            if (is_array($definition)) {
                // Something exists there already.

                if (current($definition) instanceof CompositeCommand) {
                    // Reusing if it is a composite command
                    $parent = current($definition);
                }

                continue;
            }

            $class::add_command(implode(' ', $currentScope), $currentNamespace);
        }
    }
}