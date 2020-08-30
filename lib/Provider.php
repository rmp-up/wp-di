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
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package   WpDi
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt proprietary
 * @link      https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi;

use InvalidArgumentException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RmpUp\WpDi\Provider\Parameters;
use RmpUp\WpDi\Provider\ProviderNode;
use RmpUp\WpDi\Provider\ProviderNodeTrait;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Provider\WordPress\Options;
use RmpUp\WpDi\Provider\WordPress\Templates;
use RmpUp\WpDi\Sanitizer\SanitizerInterface;

/**
 * Provider
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Provider implements ServiceProviderInterface
{
    use ProviderNodeTrait;

    /**
     * @var array
     * @deprecated Use ::$nodes instead
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
     * @param array $sanitizer     Pre-Mapping provider to sanitizer instanced (DEPRECATED 0.8)
     * @param array $keyToProvider Map a root-level key to a specific provider.
     */
    public function __construct(array $definition, array $sanitizer = [], array $keyToProvider = [])
    {
        $this->definition = $definition;
        $this->sanitizer = $sanitizer;

        $this->nodes = $keyToProvider ?: $this->defaultCompiler();
        $this->keyToCompiler =& $this->nodes; // @deprecated 0.8.0 BC
    }

    /**
     * @param string $key               Section-Key as used in the definitions.
     * @param string|callable $compiler Class name or already callable.
     *
     * @deprecated 0.8 Please use ::addProvider instead.
     */
    public function addCompiler($key, $compiler)
    {
        if (!array_key_exists($key, $this->keyToCompiler)) {
            $this->keyToCompiler[$key] = [];
        }

        $this->keyToCompiler[$key][] = $compiler;
    }

    /**
     * @param string|callable $sectionProvider A class-name or a callable.
     * @param Container       $pimple
     * @param array           $definition
     *
     * @deprecated 0.8.0 Just here for BC.
     * @internal
     */
    private function bcRegistration($sectionProvider, Container $pimple, $definition)
    {
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

    private function defaultCompiler(): array
    {
        return [
            'services' => new Services(),
            'options' => new Options(),
            'parameters' => new Parameters(),
            'templates' => new Templates(),
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
    public function register(Container $pimple)
    {
        // TODO 0.8.0 $this->validateDefinition($this->definition);

        foreach ($this->definition as $provider => $definition) {
            $sectionProvider = $this->keyToCompiler[$provider] ?? $provider;

            if ($sectionProvider instanceof ProviderNode) {
                $sectionProvider($definition, $pimple);
                continue;
            }

            $this->bcRegistration($sectionProvider, $pimple, $definition);
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
    private function sanitizer(string $provider)
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
