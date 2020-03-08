<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * KeepStringsTest.php
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
 * @since      2019-04-27
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\Services;

use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Provider\Parameters;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Test\Mirror;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * Parameters
 *
 * Primitives values that shall remain as they are can be added as parameters:
 *
 * ```yaml
 * parameters:
 *   dates: 50
 *
 * services:
 *   SomeThing:
 *     arguments: '%dates%'
 * ```
 *
 * The first argument of the class `SomeThing` is a reference
 * to the parameter, so in the end it is the same as a `new SomeThing(50)` call.
 *
 * The deprecated way of defining parameters was like this:
 *
 * ```php
 * <?php
 *
 * return [
 *   Provider\Parameters::class => [
 *     'answer' => 'XLII',
 *   ],
 *
 *   Provider\Services::class => [
 *     Question::class => [
 *       'answer',
 *     ]
 *   ]
 * ];
 * ```
 *
 * Once the service "Question" is instantiated it will receive the "answer"-parameter
 * (like `new Question('XLII')`).
 *
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 */
class DefineParametersTest extends SanitizerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        // DEPRECATED 0.7.0 - using the documented example instead
        $this->pimple->register(new Parameters([
            'foo' => 'bar',
            'bar' => 'baz',
            'qux' => 'quxx',
        ]));

        $this->pimple->register(new Provider($this->yaml(0)));
    }

    /**
     * @deprecated 0.7.0
     */
    public function testStringsStayAsTheyAre()
    {
        static::assertEquals('bar', $this->container->get('foo'));
        static::assertEquals('baz', $this->container->get('bar'));
        static::assertEquals('quxx', $this->container->get('qux'));
    }

    /**
     * @deprecated 0.7.0
     */
    public function testStringsUsedAsArgument()
    {
        $this->pimple->register(new Services([
            Mirror::class => [
                Services::CLASS_NAME => Mirror::class,
                Services::ARGUMENTS => [
                    'foo',
                    'qux',
                ]
            ]
        ]));

        /** @var Mirror $mirror */
        $mirror = $this->container->get(Mirror::class);

        static::assertEquals(['bar', 'quxx'], $mirror->getConstructorArgs());
    }

    public function testParameterDefinition()
    {
        static::assertSame(50, $this->pimple['dates']);
        static::assertSame(50, $this->pimple['%dates%']);
    }
}