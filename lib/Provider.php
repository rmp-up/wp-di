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
 * @package   WpDi
 * @copyright 2019 Mike Pretzlaw
 * @license   https://mike-pretzlaw.de/license-generic.txt proprietary
 * @link      https://project.mike-pretzlaw.de/wp-di
 * @since     2019-05-30
 */

declare(strict_types=1);

namespace RmpUp\WpDi;

use InvalidArgumentException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RmpUp\WpDi\Compiler\Filter;
use RmpUp\WpDi\Compiler\WpCli;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Sanitizer\SanitizerInterface;

/**
 * Provider
 *
 * @copyright 2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since     2019-05-30
 */
class Provider implements ServiceProviderInterface
{
    /**
     * @var array
     */
    private $keyToCompiler;
    /**
     * (Un-)Normalized definition of the services
     *
     * @var array
     */
    private $serviceDefinition;

    /**
     * Provider to sanitizer mapping.
     *
     * @var array
     */
    private $sanitizer;

    /**
     * Specific provider.
     *
     * @var array
     */
    private $provider;

    /**
     * Create generic provider
     *
     * @param array $serviceDefinition (Un-)normalized service definitions
     * @param array $sanitizer         Pre-Mapping provider to sanitizer instanced
     * @param array $keyToCompiler
     */
    public function __construct(array $serviceDefinition, array $sanitizer = [], array $keyToCompiler = [])
    {
        $this->serviceDefinition = $serviceDefinition;
        $this->sanitizer = $sanitizer;

        if ([] === $keyToCompiler) {
            $filter = [new Filter()];

            $keyToCompiler = [
                'add_filter' => $filter,
                'filter' => $filter,
                'add_action' => $filter,
                'action' => $filter,
                'wp_cli' => [new WpCli()]
            ];
        }

        $this->keyToCompiler = $keyToCompiler;
    }

    public function addCompiler($key, $compiler)
    {
        if (!array_key_exists($key, $this->keyToCompiler)) {
            $this->keyToCompiler[$key] = [];
        }

        $this->keyToCompiler[$key][] = $compiler;
    }

    /**
     * @param Container $pimple
     * @param string    $provider
     * @param array     $definition
     *
     * @deprecated Please make use of a compiler / handler (see constructor).
     */
    private function providerDelegation(Container $pimple, string $provider, array $definition)
    {
        if (false === class_exists($provider)) {
            throw new InvalidArgumentException('Unknown provider: ' . $provider);
        }

        $pimple->register(new $provider($definition));
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
            $this->providerDelegation($pimple, $provider, $this->sanitize($provider, $definition));

            if ($provider !== Services::class) {
                continue;
            }

            foreach ($definition as $serviceName => $serviceDefinition) {
                foreach (array_intersect_key($this->keyToCompiler, (array) $serviceDefinition) as $key => $extensions) {
                    // Found keywords will be forwarded to their handler.
                    // Cast to array in case one key maps directly to one compiler (instead of an array of compiler).
                    foreach ((array) $extensions as $extension) {
                        // Handler receive the specific config but also the general container for further processing.
                        $extension($serviceDefinition[$key], $serviceName, $pimple);
                    }
                }
            }
        }
    }

    /**
     * Sanitize service definition based on provider class.
     *
     * @param string $provider   Class name of the provider
     * @param array  $definition Service definition that shall be normalized
     *
     * @return array
     */
    private function sanitize(string $provider, array $definition): array
    {
        $sanitizer = $this->sanitizer($provider);

        if ($sanitizer instanceof SanitizerInterface) {
            $definition = $sanitizer->sanitize($definition);
        }

        return $definition;
    }

    /**
     * Fetch sanitizer for given provider class
     *
     * @param string $provider Class name of a provider
     *
     * @return SanitizerInterface
     */
    private function sanitizer(string $provider): ?SanitizerInterface
    {
        if (false === array_key_exists($provider, $this->sanitizer)) {
            $this->sanitizer[$provider] = null;

            $sanitizerClass = str_replace('Provider', 'Sanitizer', $provider);
            if (true === class_exists($sanitizerClass)) {
                $this->sanitizer[$provider] = new $sanitizerClass();
            }
        }

        return $this->sanitizer[$provider];
    }
}
