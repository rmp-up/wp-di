<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Mirror.php
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

namespace RmpUp\WpDi\Test;

use stdClass;

/**
 * Helper for testing that mirrors things it gets called with.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class Mirror extends stdClass
{
    /**
     * @var array
     */
    public static $staticCalls = [];

    /**
     * @var array
     */
    private $construct;

    /**
     * Keep a history to reflect later on.
     *
     * @var array
     */
    private static $history = [];

    public function __construct(...$construct)
    {
        $this->construct = $construct;

        self::record('__construct', $construct);
    }

    private static function record(string $method, array $arguments)
    {
        self::$history[] = [
            'method' => $method,
            'arguments' => $arguments,
        ];
    }

    public static function _reset() {
        self::$history = [];
        self::$staticCalls = [];
    }

    public static function _history($method = null): array
    {
        if (null === $method) {
            return self::$history;
        }

        $filtered = [];
        foreach (self::$history as $item) {
            if ($item['method'] === $method) {
                $filtered[] = $item;
            }
        }

        return $filtered;
    }

    public function __invoke(...$invoked)
    {
        self::record('__invoke', $invoked);

        return [
            'constructor' => $this->getConstructorArgs(),
            'invoked' => $invoked,
        ];
    }

    public static function __callStatic($method, $arguments)
    {
        self::record($method, $arguments);

        static::$staticCalls[] = [
            'method' => $method,
            'arguments' => $arguments,
        ];
    }

    public function __call($name, $arguments)
    {
        self::record($name, $arguments);
    }

    public function __toString()
    {
        self::record('__toString', []);

        return (string) (get_class($this) ?: 'Mirror');
    }

    /**
     * @return array
     */
    public function getConstructorArgs(): array
    {
        return $this->construct;
    }
}