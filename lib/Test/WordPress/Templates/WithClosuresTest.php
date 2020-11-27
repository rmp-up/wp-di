<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WithClosuresTest.php
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

namespace RmpUp\WpDi\Test\WordPress\Templates;

use Pimple\Container;
use ReflectionProperty;
use RmpUp\WpDi\Helper\Deprecated;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Provider\WordPress\Templates;

/**
 * Custom resolver using callback
 *
 * When you need some custom implementation then it is still possible
 * to use anonymous functions:
 *
 * ```php
 * <?php
 *
 * use \RmpUp\WpDi\Provider\WordPress;
 *
 * return [
 *   WordPress\Templates::class => [
 *     'ncc-1717.php',
 *     'ncc-1701-a.php',
 *
 *     'my-footer.php' => static function ($container) {
 *       if ( 2286 < date( 'Y' ) ) {
 *         return $container['ncc-1717.php'];
 *       }
 *
 *       return $container['ncc-1701-a.php'];
 *     }
 *   ]
 * ];
 * ```
 *
 * Using a function enables you to switch templates at a specific time and date
 * or do other fancy stuff depending on the date-time,
 * the user,
 * the current post-type
 * and many more.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class WithClosuresTest extends TemplatesTestCase
{
    /**
     * @param string         $serviceName
     * @param null|Container $container
     *
     * @return mixed
     * @throws \ReflectionException
     */
    private function getRawServiceDefinition(string $serviceName, $container = null)
    {
        if (null === $container) {
            $container = $this->pimple;
        }

        $serviceDefinition = $container->raw($serviceName);

        if ($serviceDefinition instanceof Deprecated) {
            $serviceProperty = (new ReflectionProperty($serviceDefinition, 'serviceDefinition'));
            $serviceProperty->setAccessible(true);

            return $serviceProperty->getValue($serviceDefinition);
        }

        return $serviceDefinition;
    }

    protected function setUp()
    {
        $this->sanitizer = new \RmpUp\WpDi\Sanitizer\WordPress\Templates();

        $this->definition = $this->classComment()->execute(0);
        $this->services[Templates::class] = $this->sanitizer->sanitize($this->definition[Templates::class]);

        $this->pimple = new Container();

        (new Provider())($this->services, $this->pimple);
    }

    public function testClosureUnchanged()
    {
        static::assertSame(
            $this->definition[Templates::class]['my-footer.php'],
            $this->pimple->raw('%my-footer.php%')
        );
    }
}
