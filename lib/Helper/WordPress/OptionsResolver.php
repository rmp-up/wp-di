<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OptionResolver.php
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
 * @copyright 2020 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Helper\WordPress;

use InvalidArgumentException;
use Pimple\Container;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RmpUp\WpDi\Helper\Deprecated;

/**
 * Resolve default options from the container
 *
 * This class can be invoked  in the "default_option_{$option}" filter.
 * It looks up if the container has a equally named parameter
 * and returns it.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class OptionsResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var mixed[]
     */
    private $cache = [];

    public function __construct($container)
    {
        if ($container instanceof ContainerInterface) {
            Deprecated::forwardCompatible(
                'Using PSR-11 is deprecated. Please inject a Pimple container'
            );
        }

        if ($container instanceof Container) {
            $container = new \Pimple\Psr11\Container($container);
        }

        if (false === $container instanceof ContainerInterface) {
            throw new InvalidArgumentException('Please inject a Pimple container');
        }

        $this->container = $container;
    }

    public function __invoke($currentDefault, string $option, $hasDefault)
    {
        if ($hasDefault) {
            // Default value has been passed to "get_option" already.
            return $currentDefault;
        }

        if (array_key_exists($option, $this->cache)) {
            // Keep it cached to not resolve this again.
            return $this->cache[$option];
        }

        try {
            $this->cache[$option] = $this->container->get('%' . $option . '%');
        } catch (NotFoundExceptionInterface $e) {
            $this->cache[$option] = $currentDefault;
        }

        return $this->cache[$option];
    }
}