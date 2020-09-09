<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TranslationsTest.php
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
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\Primitives\YamlExpressions;

use RmpUp\WpDi\Helper\WordPress\LazyFunctionCall;
use RmpUp\WpDi\Test\Mirror;
use RmpUp\WpDi\Test\Primitives\YamlExpressionsTest;
use SomeThing;

/**
 * Translations within Yaml
 *
 * For a few scenarios in WordPress it is necessary to translate
 * titles, menu-entries and other things.
 * The common functions `__()`, `_n()` and other
 * are available within YAML like this:
 *
 * ```yaml
 * services:
 *   SomeThing:
 *     arguments:
 *       - !__ [ Wer ist Adam?, dark ]
 *       # Same as `__('Wer ist Adam?', 'dark')` but lazy
 * ```
 *
 * Those translations won't be applied immediately.
 * They are lazy until the service is needed
 * to spare some runtime.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class TranslationsTest extends YamlExpressionsTest
{
    public function testTranslationIsLazy()
    {
        $arguments = $this->yaml(0, 'services', 'SomeThing', 'arguments');

        static::assertInstanceOf(LazyFunctionCall::class, current($arguments));
    }

    public function testIsTranslatedWithinContainer()
    {
        $this->mockFilter('gettext')
            ->expects($this->atLeastOnce())
            ->with('Wer ist Adam?', 'Wer ist Adam?', 'dark')
            ->willReturn('Nicht Eva!');

        $this->registerServices();

        /** @var Mirror $thing */
        $thing = $this->container->get(SomeThing::class);

        static::assertEquals('Nicht Eva!', $thing->getConstructorArgs()[0]);
    }
}