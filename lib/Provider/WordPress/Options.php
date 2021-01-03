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
 * @copyright 2021 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Provider\WordPress;

use Pimple\Container;
use RmpUp\WpDi\Helper\WordPress\OptionsResolver;
use RmpUp\WpDi\Provider\ProviderNode;
use RmpUp\WpDi\ServiceDefinition\OptionNode;

/**
 * Options
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class Options implements ProviderNode
{
    /**
     * @var OptionsResolver[]
     */
    private $optionsResolver = [];

    /**
     * @param Container $pimple
     *
     * @return OptionsResolver
     */
    protected function optionsResolver(Container $pimple): OptionsResolver
    {
        // Since there is a factory for the options resolver
        // we need to distinguish between multiple Pimple instances here.
        $hash = spl_object_hash($pimple);

        if (empty($this->optionsResolver[$hash])) {
            // DEPRECATED - the options resolver should be injected instead
            $this->optionsResolver[$hash] = new OptionsResolver($pimple);
        }

        return $this->optionsResolver[$hash];
    }

    public function __invoke(array $definition, Container $pimple, $key = '')
    {
        $optionResolver = $this->optionsResolver($pimple);

        foreach ($this->sanitize($definition) as $optionKey => $value) {
            if (false === is_callable($value)) {
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