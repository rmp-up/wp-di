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

namespace RmpUp\WpDi\Test\Provider\ArrayProvider;

use ArrayIterator;
use ArrayObject;
use Pimple\Container;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Sanitizer;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * FlatArrayTest
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-04-25
 */
class FlatArrayTest extends AbstractTestCase
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

        $this->pimple->register(
            new Provider\Services(
                $sanitizer->sanitize($this->services)
            )
        );
    }


    public function testHasInstance()
    {
        static::assertInstanceOf(ArrayIterator::class, $this->container->get(ArrayIterator::class));
    }
}