<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Templates.php
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

namespace RmpUp\WpDi\Provider\WordPress;

use Closure;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RmpUp\WpDi\Provider\ProviderNode;
use RmpUp\WpDi\ServiceDefinition\TemplateNode;

/**
 * Templates
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Templates implements ServiceProviderInterface, ProviderNode
{
    /**
     * Mapping of service name to array of possible templates
     *
     * @var array
     */
    private $definition;

    /**
     * Template provider
     *
     * @param array $definition (DEPRECATED 0.8) Mapping of service name to array of possible templates.
     * @deprecated 0.8.0
     */
    public function __construct($definition = [])
    {
        $this->definition = $definition;
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
        $this->__invoke($this->definition, $pimple);
    }

    public function __invoke(array $definition, Container $pimple, $key = '') {
        foreach ($definition as $templateName => $templates) {
            if (is_int($templateName) && is_string($templates)) {
                $templateName = $templates;
                $templates = (array) $templates;
            }

            if ($templates instanceof Closure) {
                // Already a function so we reuse this instead.
                $pimple[$templateName] = $templates;
                continue;
            }

            $templateNode = new TemplateNode((array) $templates);

            $pimple[$templateName] = $templateNode; // @deprecated 0.8.0
            $pimple['%' . $templateName . '%'] = $templateNode;
        }
    }
}
