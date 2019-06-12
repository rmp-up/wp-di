<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Provider.php
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
 * @since      2019-05-30
 */

declare(strict_types=1);

namespace RmpUp\WpDi;

use InvalidArgumentException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RmpUp\WpDi\Sanitizer\SanitizerInterface;

/**
 * Provider
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-05-30
 */
class Provider implements ServiceProviderInterface
{
    /**
     * @var array
     */
    private $serviceDefinition;

    /**
     * Provider to sanitizer mapping.
     *
     * @var array
     */
    private $sanitizer;

    public function __construct(array $serviceDefinition, array $sanitizer = [])
    {
        $this->serviceDefinition = $serviceDefinition;
        $this->sanitizer = $sanitizer;
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
        foreach ($this->serviceDefinition as $provider => $definition) {
            if (!class_exists($provider)) {
                throw new InvalidArgumentException('Unknown provider: ' . $provider);
            }

            $pimple->register(
                new $provider($this->sanitize($provider, $definition))
            );
        }
    }

    private function sanitize(string $provider, array $definition): array
    {
        $sanitizer = $this->sanitizer($provider);

        if ($sanitizer instanceof SanitizerInterface) {
            $definition = $sanitizer->sanitize($definition);
        }

        return $definition;
    }

    /**
     * @param string $provider
     * @return SanitizerInterface
     */
    private function sanitizer(string $provider): ?SanitizerInterface
    {
        if (!array_key_exists($provider, $this->sanitizer)) {
            $this->sanitizer[$provider] = null;

            $sanitizerClass = str_replace('Provider', 'Sanitizer', $provider);
            if (class_exists($sanitizerClass)) {
                $this->sanitizer[$provider] = new $sanitizerClass();
            }
        }

        return $this->sanitizer[$provider];
    }
}