<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ArrayDefinitions.php
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
 * @since      2019-04-25
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Provider;

use Closure;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RmpUp\WpDi\ServiceDefinition;

/**
 * Setting "services" and "parameters".
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-04-25
 */
class Services implements ServiceProviderInterface
{
    public const CLASS_NAME = 'class';
    public const ARGUMENTS = 'arguments';

    /**
     * @var callable[][]
     */
    private $keywordToHandler;

    /**
     * @var array
     */
    protected $services;

    /**
     * Services constructor.
     *
     * @param array      $services
     * @param callable[][] $keywordToHandler Mapping of keys delegating to other handler.
     */
    public function __construct(array $services, array $keywordToHandler = [])
    {
        $this->services = $services;
        $this->keywordToHandler = $keywordToHandler;
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple): void
    {
        foreach ($this->services as $key => $value) {
            $this->compile($pimple, $key, $value);
        }
    }

    /**
     * @param Container $pimple
     * @param string $serviceName
     * @param array|string|callable $definition
     */
    protected function compile(Container $pimple, string $serviceName, $definition): void
    {
        if ($definition instanceof Closure || is_callable($definition)) {
            // Already lazy
            $pimple[$serviceName] = $definition;
            return;
        }

        if (is_scalar($definition)) {
            // Remain scalar as they are.
            $pimple[$serviceName] = $definition;
            return;
        }

        $pimple[$serviceName] = new ServiceDefinition($definition);
    }
}
