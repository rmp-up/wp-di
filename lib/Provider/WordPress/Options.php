<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Options.php
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

namespace RmpUp\WpDi\Provider\WordPress;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RmpUp\WpDi\Helper\WordPress\OptionsResolver;
use RmpUp\WpDi\Provider\ProviderNode;
use RmpUp\WpDi\ServiceDefinition\OptionNode;

/**
 * Options
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Options implements ServiceProviderInterface, ProviderNode
{
    /**
     * @var OptionsResolver
     */
    private $optionsResolver;
    /**
     * @var array
     */
    private $serviceDefinition;

    /**
     * Options constructor.
     *
     * @param array           $serviceDefinition
     */
    public function __construct(array $serviceDefinition = [])
    {
        $this->serviceDefinition = $serviceDefinition;
    }

    /**
     * @param Container $pimple
     *
     * @return OptionsResolver
     */
    protected function optionsResolver(Container $pimple): OptionsResolver
    {
        if (null === $this->optionsResolver) {
            // DEPRECATED - the options resolver should be injected instead
            $this->optionsResolver = new OptionsResolver(new \Pimple\Psr11\Container($pimple));
        }

        return $this->optionsResolver;
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     * @deprecated 0.8.0 Use ::__invoke instead.
     */
    public function register(Container $pimple)
    {
        $this->__invoke($this->serviceDefinition, $pimple);
    }

    public function __invoke(array $definition, Container $pimple, $key = '')
    {
        $optionResolver = $this->optionsResolver($pimple);

        foreach ($this->sanitize($definition) as $optionKey => $value) {
            if (!is_callable($value)) {
                $value = new OptionNode($optionKey, $value);
            }

            $pimple['%' . $optionKey . '%'] = $value;

            add_filter('default_option_' . $optionKey, $optionResolver, 10, 3);
        }
    }

    private function sanitize($node): array
    {
        $sanitized = [];

        foreach ($node as $optionKey => $optionsValue) {
            if (is_int($optionKey)) {
                $optionKey = $optionsValue;
                $optionsValue = OptionNode::DEFAULT;
            }

            $sanitized[$optionKey] = $optionsValue;
        }

        return $sanitized;
    }
}