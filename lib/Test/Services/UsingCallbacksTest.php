<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * UsingCallbacksTest.php
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

use ArrayObject;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Sanitizer\Services;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * Custom service using lambda function
 *
 * As known from Pimple you can also define custom services using
 * closures:
 *
 * ```php
 * <?php
 *
 * use RmpUp\WpDi\Provider\Services;
 *
 * return [
 *   Services::class => [
 *     'random_things' => function () {
 *
 *       return new ArrayObject([
 *         'int' => random_int(1,9),
 *         'string' => str_shuffle('anis'),
 *       ]);
 *
 *     }
 *   ]
 * ];
 * ```
 *
 * But you can also reuse existing services within your own definition
 * because the closure gets the Pimple-Container as first argument.
 *
 * ```php
 * <?php
 *
 * use RmpUp\WpDi\Provider\Services;
 * use RmpUp\WpDi\Provider\Parameters;
 *
 * return [
 *   Parameters::class => [
 *     'some_int' => random_int(1,9),
 *     'some_string' => 'ansi',
 *   ],
 *
 *   Services::class => [
 *     'more_random_things' => function ($container) {
 *
 *       return new ArrayObject([
 *         'int' => $container['%some_int%'] * 42,
 *         'string' => 'xoxo' . $container['%some_string%'] . '<3',
 *       ]);
 *
 *     }
 *   ]
 * ];
 * ```
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class UsingCallbacksTest extends SanitizerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->sanitizer = new Services();

        $provider = new Provider([]);

        $provider->__invoke(
            array_merge_recursive(
                $this->classComment()->execute(0),
                $this->classComment()->execute(1)
            ),
            $this->pimple
        );
    }

    public function testSimpleClosure()
    {
        $this->assertRandomThing($this->pimple['random_things']);
    }

    public function testClosureWithReference()
    {
        $this->assertRandomThing($this->pimple['random_things']);

        static::assertInternalType('int', $this->pimple['%some_int%']);
        static::assertSame($this->pimple['%some_int%'] * 42, $this->pimple['more_random_things']['int']);
        static::assertSame('xoxo' . $this->pimple['%some_string%'] . '<3', $this->pimple['more_random_things']['string']);
    }

    /**
     * @param $things
     */
    private function assertRandomThing($things)
    {
        static::assertInstanceOf(ArrayObject::class, $things);

        static::assertInternalType('string', $things['string']);
        static::assertRegExp('/[anis]{4}/', $things['string']);

        static::assertInternalType('int', $things['int']);
        static::assertLessThan(10, $things['int']);
        static::assertGreaterThan(0, $things['int']);
    }
}