<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * LazyInstanceTest.php
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

namespace RmpUp\WpDi\Test\Helper;

use RmpUp\WpDi\Helper\LazyInstantiating;
use RmpUp\WpDi\Test\AbstractTestCase;
use RmpUp\WpDi\Test\Mirror;
use stdClass;

/**
 * LazyInstanceTest
 *
 * @internal
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class LazyInstanceTest extends AbstractTestCase
{
    private $constructorCall = [
        'method' => '__construct',
        'arguments' => [],
    ];

    private $lazyInstance;

    /**
     * @var Mirror
     */
    private $proxyTarget;

    protected function setUp()
    {
        $this->proxyTarget = new Mirror();
        $this->lazyInstance = new class ($this->proxyTarget) {
            use LazyInstantiating;

            private $mirror;

            public $isCreated = false;

            public function __construct($mirror)
            {
                $this->mirror = $mirror;
            }

            protected function createProxyObject()
            {
                $this->isCreated = true;
                return $this->mirror;
            }
        };
        parent::setUp();
    }

    public function testCallMethod()
    {
        $this->lazyInstance->callMeOnMyCellphone();

        static::assertEquals(
            [
                $this->constructorCall,
                [ // then the actual method
                    'method' => 'callMeOnMyCellphone',
                    'arguments' => [],
                ]
            ],
            $this->proxyTarget->_history()
        );
    }

    public function testInvoke()
    {
        static::assertEquals([$this->constructorCall], $this->proxyTarget->_history());

        ($this->lazyInstance)('en vogue');

        static::assertEquals(
            [
                $this->constructorCall,
                [
                    'method' => '__invoke',
                    'arguments' => ['en vogue']
                ]
            ],
            $this->proxyTarget->_history()
        );
    }

    public function testIsLazy()
    {
        static::assertFalse($this->lazyInstance->isCreated);
        $this->lazyInstance->foo();
        static::assertTrue($this->lazyInstance->isCreated);
    }

    public function testSetData()
    {
        static::assertFalse(isset($this->lazyInstance->something));

        $expected = uniqid('', true);
        $this->lazyInstance->something = $expected;

        static::assertTrue(isset($this->lazyInstance->something));
        static::assertEquals($expected, $this->proxyTarget->something);
        static::assertEquals($expected, $this->lazyInstance->something);
    }
}