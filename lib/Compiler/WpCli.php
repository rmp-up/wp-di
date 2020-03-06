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
use RmpUp\WpDi\LazyService;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Provider\WordPress\CliCommands;

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
        if (is_array($commandToMethod) && array_key_exists(CliCommands::COMMAND, $commandToMethod)) {
            // DEPRECATED 0.6 structure will be ignored
            return;
        }

        if (is_scalar($commandToMethod)) {
            $commandToMethod = [$commandToMethod => null];
        }

        $container = new \Pimple\Psr11\Container($pimple);

        foreach ($commandToMethod as $command => $method) {
            if (null === $method) {
                $method = '__invoke';
            }

            $class = $this->wpCliClass;

            $serviceDefinition = $pimple->raw($serviceName);

            if (empty($serviceDefinition[Services::ARGUMENTS])) {
                /** @noinspection PhpUndefinedMethodInspection */
                $class::add_command($command, $serviceDefinition[Services::CLASS_NAME]);
                continue;
            }

            if ($pimple->offsetExists($serviceName)) {
                // Command is wired to existing service.
                /** @noinspection PhpUndefinedMethodInspection */
                $class::add_command($command, [new LazyService($container, $serviceName), $method]);
                continue;
            }
        }
    }
}