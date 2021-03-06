<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ServiceDefinition.php
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
 * @copyright 2021 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WpDi;

use ArrayObject;
use Pimple\Container;
use RmpUp\WpDi\Helper\Check;
use RmpUp\WpDi\Helper\LazyInvoke;
use RmpUp\WpDi\Helper\LazyPimple;
use RmpUp\WpDi\Provider\Services;

/**
 * Carrying service definitions for late usage
 *
 * From defining a service to using it can be a long way
 * or plenty lines of code.
 * There are use cases (like for wp-cli) where the very early
 * set service definition is needed later on.
 *
 * Usually you would give Pimple a closure that just creates the service/object.
 * But this way all information about the definition would be gone.
 * With the ServiceDefinition we carry the definition itself into Pimple
 * and make it accessible for other services, compiler
 * or provider (by using `Pimple::raw`).
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class ServiceDefinition extends ArrayObject
{
    private static $referenceCache = [];

    /**
     * Default factory to create objects
     *
     * @param mixed ...$arguments
     *
     * @return mixed
     */
    private function createObject(...$arguments)
    {
        $className = $this[Services::CLASS_NAME];

        if (empty($arguments)) {
            return new $className();
        }

        return new $className(...$arguments);
    }

    /**
     * Delegate to appropriate factory
     *
     * @param Container $pimple
     *
     * @return mixed
     */
    public function __invoke(Container $pimple)
    {
        $arguments = null;
        if (isset($this[Services::ARGUMENTS])) {
            $arguments = $this->resolve($pimple, $this[Services::ARGUMENTS]);
        }

        $factory = [$this, 'createObject'];
        if (isset($this['factory'])) {
            $factory = $this->resolve($pimple, $this['factory']);
        }

        if (null === $arguments) {
            return $factory();
        }

        return $factory(...array_values($arguments));
    }

    /**
     * @param Container $pimple
     * @param array|string $definition
     *
     * @return mixed|LazyPimple|string
     */
    private function resolve($pimple, $definition)
    {
        $resolved = $definition;
        if (is_string($definition)) {
            return $this->resolveParameter($pimple, $definition);
        }

        foreach ($definition as $key => $argument) {
            if (empty($argument)) {
                // Skip empty
                continue;
            }

            if (is_string($argument)) {
                $resolved[$key] = $this->resolveParameter($pimple, $argument);
            }

            if ($argument instanceof LazyInvoke) {
                $resolved[$key] = $argument();
            }
        }

        return $resolved;
    }

    /**
     * @param Container $pimple The container to lookup.
     * @param string    $parameter
     *
     * @return mixed
     */
    private function resolveParameter(Container $pimple, string $parameter)
    {
        if (isset($pimple[$parameter])) {
            return $pimple[$parameter];
        }

        switch ($parameter[0]) {
            case '@':
                $argument = substr($parameter, 1);
                if ('@' === $argument[0]) {
                    return $argument;
                }

                if (false === empty($this[Services::LAZY_ARGS])) {
                    return new LazyPimple($pimple, $argument);
                }

                return $pimple[$argument];

            case '%':
                if (Check::isReferenceToParameter($parameter)) {
                    // Not found in Pimple so we fallback to options
                    return $this->resolveReference($parameter);
                }

                return $parameter;

            default:
                return $parameter;
        }
    }

    private function resolveReference(string $parameter)
    {
        if (false === array_key_exists($parameter, self::$referenceCache)) {
            self::$referenceCache[$parameter] = get_option(trim($parameter, '%'), $parameter);
        }

        return self::$referenceCache[$parameter];
    }
}