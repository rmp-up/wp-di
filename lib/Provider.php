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

use RmpUp\WpDi\Provider\Parameters;
use RmpUp\WpDi\Provider\ProviderNodeTrait;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Provider\WordPress\Options;
use RmpUp\WpDi\Provider\WordPress\Templates;

/**
 * Provider
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Provider
{
    use ProviderNodeTrait;

    /**
     * Create provider
     *
     * This provider delegates sections of a service definition to several other provider.
     *
     * @param array $keyToProvider Map a root-level key to a specific provider.
     */
    public function __construct(array $keyToProvider = [])
    {
        if ([] === $keyToProvider) {
            $keyToProvider = $this->defaultCompiler();
        }

        $this->nodes = $keyToProvider;
    }

    private function defaultCompiler(): array
    {
        $defaults = [
            'services' => new Services(),
            'options' => new Options(),
            'parameters' => new Parameters(),
            'templates' => new Templates(),
        ];

        $defaults[Services::class] = $defaults['services'];
        $defaults[Options::class] = $defaults['options'];
        $defaults[Parameters::class] = $defaults['parameters'];
        $defaults[Templates::class] = $defaults['templates'];

        return $defaults;
    }
}
