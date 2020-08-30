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

namespace RmpUp\WpDi\Test;

use Closure;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Pretzlaw\WPInt\Traits\WordPressTests;
use ReflectionException;
use ReflectionObject;
use RmpUp\Doc\DocParser;
use RmpUp\WpDi\Helper\LazyPimple;
use RmpUp\WpDi\LazyService;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Yaml;

/**
 * AbstractTestCase
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
abstract class AbstractTestCase extends TestCase
{
    use DocParser;
    use WordPressTests;

    /**
     * @var Container
     */
    protected $pimple;
    protected $services = [];

    public static $actions = [];
    public static $calls = [];

    /**
     * @var \Pimple\Psr11\Container
     */
    protected $container;

    protected function setUp()
    {
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

    /**
     * @param string $filterName
     *
     * @return \WP_Hook|null
     */
    protected function getFilter(string $filterName)
    {
        global $wp_filter;

        return $wp_filter[$filterName] ?? null;
    }

    protected static function assertLazyService(string $serviceName, $lazyServiceObject)
    {
        static::assertContains(get_class($lazyServiceObject), [LazyService::class, LazyPimple::class]);
        static::assertEquals($serviceName, self::getField($lazyServiceObject, 'serviceName'));
    }

    protected function assertServiceNotLoaded(string $serviceName)
    {
        $values = self::getField($this->pimple, 'values');
        static::assertArrayHasKey($serviceName, $values, 'Service unknown');
        static::assertInstanceOf(Closure::class, $values[$serviceName]);
    }

    protected function assertServiceLoaded(string $serviceName)
    {
        $values = self::getField($this->pimple, 'values');
        static::assertArrayHasKey($serviceName, $values, 'Service unknown');
        static::assertNotInstanceOf(Closure::class, $values[$serviceName]);
    }

    protected function isServiceFrozen(Container $pimple, string $serviceName)
    {
        $property = (new ReflectionObject($pimple))->getProperty('frozen');

        $property->setAccessible(true);
        $list = $property->getValue($pimple);
        $property->setAccessible(false);

        return $list[$serviceName] ?? false;
    }

    protected function tearDown()
    {
        parent::tearDown();

        static::$calls = [];
        static::$actions = [];
        Mirror::_reset();
    }

    /**
     * Register services as defined in the doc-comment
     *
     * @param int   $index
     * @param mixed ...$keys
     */
    protected function registerServices($index = 0, ...$keys)
    {
        $this->pimple->register((new Provider($this->yaml($index, ...$keys))));
    }

    protected function yaml($index = 0, ...$keys)
    {
        $allNodes = $this->classComment()->xpath('//code[@class="yaml"]');

        if (!isset($allNodes[$index])) {
            throw new \DomainException('Yaml example missing: ' . $index);
        }

        $data = Yaml::parse((string) $allNodes[$index]);

        if ([] === $keys) {
            return $data;
        }

        $path = [];
        foreach ($keys as $key) {
            $path[] = $key;

            if (!array_key_exists($key, $data)) {
                throw new \DomainException(
                    sprintf(
                        'Path "%s" does not exist in %d. Yaml example',
                        implode('.', $path),
                        $index
                    )
                );
            }

            $data = $data[$key];
        }

        return $data;
    }

    protected function mockOption($name)
    {
        return $this->mockFilter('pre_option_' . $name);
    }
}
