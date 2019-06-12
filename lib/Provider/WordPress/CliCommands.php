<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * CliCommands.php
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
 * @since      2019-06-12
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Provider\WordPress;

use Pimple\Container;
use Psr\Container\ContainerInterface;
use RmpUp\WpDi\LazyService;
use RmpUp\WpDi\Provider\Services;

/**
 * CliCommands
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-12
 */
class CliCommands extends Services
{
    const COMMAND = 'command';
    const KEY = 'wp_cli';

    /**
     * @var string
     */
    private $wpCliClass;

    public function __construct(array $services, string $wpCliClass = null)
    {
        parent::__construct($services);

        if (null === $wpCliClass) {
            $wpCliClass = \WP_CLI::class;
        }

        $this->wpCliClass = $wpCliClass;
    }

    public function register(Container $pimple)
    {
        if (!class_exists($this->wpCliClass)) {
            // Wrong context.
            return;
        }

        parent::register($pimple);

        $container = new \Pimple\Psr11\Container($pimple);

        foreach ($this->services as $serviceName => $service) {
            if (!array_key_exists(static::KEY, $service) || !is_string($serviceName)) {
                continue;
            }

            if (array_key_exists(static::COMMAND, $service[static::KEY])) {
                $this->addCommand($container, $serviceName, $service);
            }
        }
    }

    private function addCommand(ContainerInterface $container, string $serviceName, array $definition)
    {
        $command = $definition[static::KEY][static::COMMAND];
        $class = $this->wpCliClass;

        if (!$definition[Services::ARGUMENTS]) {
            $class::add_command($command, $definition[Services::CLASS_NAME]);
            return;
        }

        $class::add_command($command, new LazyService($container, $serviceName));
    }
}