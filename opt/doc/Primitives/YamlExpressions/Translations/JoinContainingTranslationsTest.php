<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * JoinContainingTranslationsTest.php
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

namespace RmpUp\WpDi\Test\Primitives\YamlExpressions\Translations;

use RmpUp\WpDi\Helper\LazyInvoke;
use RmpUp\WpDi\Helper\WordPress\LazyFunctionCall;
use RmpUp\WpDi\Helper\Yaml\Implode;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * Translations can also be
 * inside a `!join` tag for example
 * and still preserve their laziness:
 *
 * ```yaml
 * services:
 *   SomeThing:
 *     arguments:
 *       - !join [ !__ [ "Hello", "foo" ] , " there!" ]
 * ```
 *
 * In this example "Hello" will be translated (using the textdomain `foo`)
 * and then "World!" will be appended (by the `!join` tag).
 * Not while parsing the YAML but as soon as the service is used.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class JoinContainingTranslationsTest extends AbstractTestCase
{
    /**
     * @return mixed|LazyFunctionCall
     */
    private function getFirstParameter()
    {
        return $this->yaml(0, 'services', 'SomeThing', 'arguments', 0);
    }

    public function testJoinKeptLazy()
    {
        static::assertFalse(is_string($this->getFirstParameter()));
    }

    public function testSomeThingHasFullConcatenatedTranslation()
    {
        $lazyFunction = $this->getFirstParameter();

        static::assertInstanceOf(LazyInvoke::class, $lazyFunction);
        static::assertInstanceOf(Implode::class, $lazyFunction);
        static::assertEquals('Hello there!', $lazyFunction());
    }
}