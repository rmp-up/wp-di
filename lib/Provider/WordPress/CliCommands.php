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
use ReflectionClass;
use RmpUp\WpDi\LazyService;
use RmpUp\WpDi\Provider\MissingServiceDefinitionException;
use RmpUp\WpDi\Provider\Services;
use WP_CLI;

/**
 * CliCommands
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-12
 */
class CliCommands extends Services
{
    public const COMMAND = 'command';
    public const KEY = 'wp_cli';

    /**
     * @var string
     */
    private $wpCliClass;

    public function __construct(array $services, string $wpCliClass = null)
    {
        parent::__construct($services);

        if (null === $wpCliClass) {
            $wpCliClass = WP_CLI::class;
        }

        $this->wpCliClass = $wpCliClass;
    }

    /**
     * @param array $definition
     *
     * @return mixed[]
     * @throws \ReflectionException
     */
    private function createWpCliConfig($definition): array
    {
        $callable = (new ReflectionClass($definition[Services::CLASS_NAME]))->getMethod('__invoke');
        $comment = (string) $callable->getDocComment();

        if ('' === $comment) {
            // There is no doc comment
            return [];
        }

        $docParser = new WP_CLI\DocParser($comment);

        $config = [
            'shortdesc' => $docParser->get_shortdesc(),
            'longdesc' => $docParser->get_longdesc(),
            'synopsis' => $docParser->get_synopsis()
        ];

        $when = $docParser->get_tag('when');
        if ($when) {
            $config['when'] = $when;
        }

        return $config;
    }

    public function register(Container $pimple): void
    {
        if ('cli' !== PHP_SAPI || !class_exists($this->wpCliClass)) {
            // Wrong context.
            return;
        }

        foreach ($this->services as $serviceName => $service) {
            if (!array_key_exists(static::KEY, $service) || !is_string($serviceName)) {
                continue;
            }

            if (!$pimple->offsetExists($serviceName)) {
                // Service config has not been loaded yet for this part.
                $this->compile($pimple, $serviceName, $service);
            }

            if (array_key_exists(static::COMMAND, $service[static::KEY])) {
                $this->addCommand($pimple, $serviceName, $service);
            }
        }
    }

    private function addCommand(Container $pimple, string $serviceName, array $definition): void
    {
        $container = new \Pimple\Psr11\Container($pimple);

        $command = $definition[static::KEY][static::COMMAND];
        $class = $this->wpCliClass;

        // Command points to a service
        if ($container->has($serviceName)) {
            /** @noinspection PhpUndefinedMethodInspection */
            $class::add_command(
                $command,
                new LazyService($container, $serviceName),
                $this->createWpCliConfig($pimple->raw($serviceName))
            );

            return;
        }

        // Command points to a class
        if (!$definition[Services::ARGUMENTS] && class_exists($definition[Services::CLASS_NAME])) {
            /** @noinspection PhpUndefinedMethodInspection */
            $class::add_command($command, $definition[Services::CLASS_NAME]);
            return;
        }

        throw new MissingServiceDefinitionException('Unknown service or class: ' . $serviceName);
    }
}
