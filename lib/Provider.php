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
use RmpUp\WpDi\Provider\Parameters;
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
    private $definition;

    /**
     * Provider to sanitizer mapping.
     *
     * @var array
     */
    private $sanitizer;

    /**
     * Create provider
     *
     * This provider delegates sections of a service definition to several other provider.
     *
     * @param array $definition    (Un-)normalized definitions of services, parameter or other
     * @param array $sanitizer     Pre-Mapping provider to sanitizer instanced (DEPRECATED)
     * @param array $keyToProvider Map a root-level key to a specific provider.
     */
    public function __construct(array $definition, array $sanitizer = [], array $keyToProvider = [])
    {
        $this->definition = $definition;
        $this->sanitizer = $sanitizer;

        if ([] !== $sanitizer) {
            trigger_error('Using sanitizer here is deprecated. Please sanitize within the compiler.', E_USER_DEPRECATED);
        }

        $this->keyToCompiler = $keyToProvider ?: $this->defaultCompiler();
    }

    public function addCompiler($key, $compiler)
    {
        if (!array_key_exists($key, $this->keyToCompiler)) {
            $this->keyToCompiler[$key] = [];
        }

        $this->keyToCompiler[$key][] = $compiler;
    }

    private function defaultCompiler(): array
    {
        return [
            'services' => Services::class,
            'parameters' => Parameters::class,
        ];
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
        foreach ($this->definition as $provider => $definition) {
            $sectionProvider = $this->keyToCompiler[$provider] ?? $provider;

            if (is_callable($sectionProvider)) {
                // Keeping provider lazy in case they are not used at all.
                $sectionProvider = $sectionProvider($pimple);
            }

            if (false === class_exists($sectionProvider)) {
                throw new InvalidArgumentException('Unknown provider: ' . $sectionProvider);
            }

            $pimple->register(
                new $sectionProvider(
                    $this->sanitize($sectionProvider, $definition)
                )
            );
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
