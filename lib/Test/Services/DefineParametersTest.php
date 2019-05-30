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

use RmpUp\WpDi\Provider\Parameters;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Test\AbstractTestCase;
use RmpUp\WpDi\Test\Mirror;

/**
 * Parameters
 *
 * Primitives values that shall remain as they are can be added as parameters:
 *
 * ```php
 * <?php
 *
 * return [
 *   Provider\Parameters::class => [
 *     'answer' => 'XLII',
 *   ]
 * ];
 * ```
 *
 * Such key-value-data can be injected in other services:
 *
 * ```php
 * <?php
 *
 * return [
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
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-04-27
 */
class DefineParametersTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->pimple->register(new Parameters([
            'foo' => 'bar',
            'bar' => 'baz',
            'qux' => 'quxx',
        ]));
    }

    public function testStringsStayAsTheyAre()
    {
        static::assertEquals('bar', $this->container->get('foo'));
        static::assertEquals('baz', $this->container->get('bar'));
        static::assertEquals('quxx', $this->container->get('qux'));
    }

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
}