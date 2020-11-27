<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FlatArrayTest.php
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

namespace RmpUp\WpDi\Test\Services;

use ArrayIterator;
use ArrayObject;
use Pimple\Container;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Sanitizer;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * Service definition
 *
 * Services can be defined using a very simple data structure:
 *
 * ```php
 * <?php
 *
 * return [
 *   Provider\Services => [
 *
 *     SomeThing::class,
 *
 *   ]
 * ];
 * ```
 *
 * This kind of definition registers one service named "SomeThing"
 * which points to an instance of same class.
 * The second is named "AnotherThing"
 * and will have the three listed arguments in the constructor.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class NoArgumentsTest extends AbstractTestCase
{
    protected function setUp()
    {
        $this->services = [
            ArrayObject::class,
            ArrayIterator::class => [
                ArrayObject::class,
                ArrayIterator::STD_PROP_LIST
            ]
        ];

        $sanitizer = new Sanitizer\Services();
        $this->pimple = new Container();
        $this->container = new \Pimple\Psr11\Container($this->pimple);

        (new Provider\Services())($this->services, $this->pimple);
    }


    public function testHasInstance()
    {
        static::assertInstanceOf(ArrayIterator::class, $this->container->get(ArrayIterator::class));
    }
}