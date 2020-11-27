<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * LazyInstantiating.php
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

namespace RmpUp\WpDi\Helper;

/**
 * Proxy all calls, read and writes to another object
 *
 * @package RmpUp\WpDi\Helper
 */
trait LazyInstantiating
{
    protected $proxyObject;

    public function __call($name, $arguments)
    {
        return $this->proxyObject()->$name(...$arguments);
    }

    public function __get($name)
    {
        return $this->proxyObject()->{$name};
    }

    public function __invoke(...$arguments)
    {
        return ($this->proxyObject())(...$arguments);
    }

    public function __isset($name)
    {
        return isset($this->proxyObject()->{$name});
    }

    public function __set($name, $value)
    {
        $this->proxyObject()->$name = $value;
    }

    public function __toString()
    {
        return (string) $this->proxyObject();
    }

    /**
     * @return object
     */
    abstract protected function createProxyObject();

    protected function proxyObject()
    {
        if (null === $this->proxyObject) {
            $this->proxyObject = $this->createProxyObject();
        }

        return $this->proxyObject;
    }
}