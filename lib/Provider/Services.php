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

namespace RmpUp\WpDi\Provider;

use Closure;
use Pimple\Container;
use RmpUp\WpDi\Compiler\Filter;
use RmpUp\WpDi\Compiler\MetaBox;
use RmpUp\WpDi\Compiler\PostType;
use RmpUp\WpDi\Compiler\Shortcode;
use RmpUp\WpDi\Compiler\Widgets;
use RmpUp\WpDi\Compiler\WpCli;
use RmpUp\WpDi\ServiceDefinition;

/**
 * Setting "services" and "parameters".
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Services implements ProviderNode
{
    const CLASS_NAME = 'class';
    const ARGUMENTS = 'arguments';
    const LAZY_ARGS = 'lazy_arguments';

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
     * @param array        $services
     * @param callable[][] $keywordToHandler Mapping of keys delegating to other handler.
     */
    public function __construct(array $services = [], array $keywordToHandler = [])
    {
        $this->services = $services;

        if ([] === $keywordToHandler) {
            $keywordToHandler = $this->defaultHandler();
        }

        $this->keywordToHandler = $keywordToHandler;
    }

    /**
     * Default handler for predefined keywords
     *
     * Mostly here as forward compatibility
     * until handler can be properly injected.
     *
     * @return array
     */
    private function defaultHandler(): array
    {
        $filter = [new Filter()];
        $shortcodes = [new Shortcode()];

        return [
            Filter::FILTER_KEY => $filter,
            'add_filter' => $filter, // alias

            Filter::ACTION_KEY => $filter,
            'add_action' => $filter, // alias

            'meta_box' => [new MetaBox()],

            'post_type' => [new PostType()],

            Shortcode::KEY => $shortcodes,
            'add_shortcode' => $shortcodes,

            'widgets' => [new Widgets()],
            'wp_cli' => [new WpCli()],
        ];
    }

    /**
     * @param array     $definition
     * @param string    $serviceName
     * @param Container $pimple
     */
    protected function delegate(array $definition, string $serviceName, Container $pimple)
    {
        // Delegate to extensions
        foreach (array_intersect_key($this->keywordToHandler, $definition) as $key => $extensions) {
            // Found keywords will be forwarded to their handler.
            // Cast to array in case one key maps directly to one compiler (instead of an array of compiler).
            foreach ((array) $extensions as $extension) {
                // Handler receive the specific config but also the general container for further processing.
                $extension($definition[$key], $serviceName, $pimple);
            }
        }
    }

    /**
     * @param Container         $pimple
     * @param string            $serviceName
     * @param array|object|null $definition
     *
     * @deprecated 0.8.0 Will become private
     * @deprecated 0.9.0 Will be completely moved into ::__invoke
     */
    protected function compile(Container $pimple, string $serviceName, $definition)
    {
        if (
            $definition instanceof Closure
            || is_callable($definition)
            || is_object($definition)
        ) {
            // Already lazy or an instance
            $pimple[$serviceName] = $definition;
            return;
        }

        if (null === $definition) {
            $definition = [];
        }

        if (empty($definition['class'])) {
            $definition['class'] = $serviceName;
        }

        $pimple[$serviceName] = new ServiceDefinition($definition);
        $this->delegate((array) $definition, $serviceName, $pimple);
    }

    public function __invoke(array $definition, Container $pimple, $key = '')
    {
        foreach ($definition as $serviceName => $value) {
            if (is_int($serviceName)) {
                $serviceName = $value;
                $value = null;
            }

            $this->compile($pimple, $serviceName, $value);
        }
    }
}
