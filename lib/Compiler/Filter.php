<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Filter.php
 *
 * LICENSE: This source file is created by the company around M. Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package   wp-di
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Compiler;

use Pimple\Container;
use RmpUp\WpDi\Helper\LazyPimple;

/**
 * Filter or action
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Filter implements CompilerInterface
{
    /** @var string Key to use in service definitions (to indicate a filter) */
    const FILTER_KEY = 'filter';

    /** @var string Key to use in service definitions (to indicate an action)  */
    const ACTION_KEY = 'action';

    /**
     * @var string
     */
    private $defaultMethod;
    /**
     * @var int
     */
    private $defaultPriority;

    public function __construct(int $defaultPriority = 10, string $defaultMethod = '__invoke')
    {
        $this->defaultPriority = $defaultPriority;
        $this->defaultMethod = $defaultMethod;
    }

    public function __invoke($definition, string $serviceName, Container $pimple)
    {
        $definition = $this->sanitize($definition);

        foreach ($definition as $filterName => $prioToCallback) {
            foreach ($prioToCallback as $priority => $methodNames) {
                $this->register($filterName, $pimple, $serviceName, $methodNames, $priority);
            }
        }
    }

    /**
     * @param string        $filterName
     * @param Container     $container (DEPRECATED 0.8 - Will use Pimple container instead)
     * @param string        $serviceName
     * @param string[]|null $methodNames
     * @param int           $priority
     */
    private function register($filterName, Container $container, string $serviceName, $methodNames, $priority)
    {
        if (null === $methodNames) {
            $methodNames = [null];
        }

        // could be still just a method name
        $methodNames = (array) $methodNames;

        foreach ($methodNames as $methodName) {
            $lazyCallback = new LazyPimple($container, $serviceName);
            if (null !== $methodName) {
                $lazyCallback = [$lazyCallback, $methodName];
            }

            add_filter($filterName, $lazyCallback, $priority, PHP_INT_MAX);
        }
    }

    private function sanitize($definition)
    {
        if (is_scalar($definition)) {
            $definition = [$definition => null];
        }

        foreach ($definition as $filterName => $prioToCallback) {
            if (null === $prioToCallback) {
                $prioToCallback = [
                    $this->defaultPriority => $this->defaultMethod,
                ];
            }

            if (is_scalar($prioToCallback)) {
                $prioToCallback = [
                    $this->defaultPriority => $prioToCallback
                ];
            }

            $definition[$filterName] = $prioToCallback;
        }

        return $definition;
    }
}