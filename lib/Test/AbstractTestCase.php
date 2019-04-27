<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbstractTestCase.php
 *
 * LICENSE: This source file is created by the company around Mike Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://mike-pretzlaw.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@mike-pretzlaw.de so we can mail you a copy.
 *
 * @package    wp-di
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-di
 * @since      2019-04-25
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test;

use Closure;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use ReflectionException;
use ReflectionObject;
use RmpUp\WpDi\LazyService;

/**
 * AbstractTestCase
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-04-25
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * @var Container
     */
    protected $pimple;
    protected $services = [];

    public static $actions = [];

    /**
     * @var \Pimple\Psr11\Container
     */
    protected $container;

    protected function setUp()
    {
        parent::setUp();

        $this->pimple = new Container();
        $this->container = new \Pimple\Psr11\Container($this->pimple);
    }

    private static function getField($object, string $parameterName)
    {
        $reflect = new ReflectionObject($object);

        try {
            $property = $reflect->getProperty($parameterName);
            $property->setAccessible(true);

            return $property->getValue($object);
        } catch (ReflectionException $e) {
            static::fail('Failed getting property: ' . $e->getMessage());
        }

        return null;
    }

    protected static function assertLazyService(string $serviceName, $lazyServiceObject)
    {
        static::assertInstanceOf(LazyService::class, $lazyServiceObject);

        static::assertEquals($serviceName, static::getField($lazyServiceObject, 'serviceName'));
    }

    protected function assertServiceNotLoaded(string $serviceName)
    {
        $values = static::getField($this->pimple, 'values');
        static::assertArrayHasKey($serviceName, $values, 'Service unknown');
        static::assertInstanceOf(Closure::class, $values[$serviceName]);
    }

    protected function assertServiceLoaded(string $serviceName)
    {
        $values = static::getField($this->pimple, 'values');
        static::assertArrayHasKey($serviceName, $values, 'Service unknown');
        static::assertNotInstanceOf(Closure::class, $values[$serviceName]);
    }

    protected function tearDown()
    {
        parent::tearDown();

        static::$actions = [];
    }
}