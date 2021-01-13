<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * EscapingTest.php
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

namespace RmpUp\WpDi\Test\Primitives\YamlExpressions;

use RmpUp\WpDi\Test\AbstractTestCase;
use RmpUp\WpDi\Test\Mirror;

/**
 * Escaping
 *
 * Usually the "@" sign indicates a service as shown below.
 * But when a string shall start with an @ it needs to be escaped using "@@":
 *
 * ```yaml
 * parameters:
 *   the_param: '@Parameter do not need such escaping@'
 *
 * services:
 *   SomeThing: ~
 *
 *   SomeThingElse:
 *     arguments:
 *       - '@SomeThing'
 *       - '@@twig-namespace/foo/bar.html.twig'
 *       - '%the_param%'
 * ```
 *
 * This only needs to be done in the beginning
 * while any other @ character does not need to be escaped afterwards.
 * We do this a bit inconsistent because the YAML should be close to the
 * well known Symfony services YAML which has the same inconsistency:
 * https://github.com/symfony/symfony/pull/7357/files#diff-942ccd0095dbe8010523f440b62cc19bc9b56357ea3a6f78b5a9e86faf56be38R293
 * Parameters do not need escaping of the first At-Char.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class EscapingTest extends AbstractTestCase
{
    protected function compatSetUp()
    {
        parent::compatSetUp();

        $this->registerServices();
    }

    public function testDoubleAtCharHasBeenEscaped()
    {
        /** @var Mirror $service */
        $service = $this->pimple[\SomeThingElse::class];

        static::assertEquals('@twig-namespace/foo/bar.html.twig', $service->getConstructorArgs()[1]);
    }

    public function testSingleAtCharResolvesToService()
    {
        /** @var Mirror $service */
        $service = $this->pimple[\SomeThingElse::class];

        static::assertSame($this->pimple[\SomeThing::class], $service->getConstructorArgs()[0]);
    }

    public function testParameterStayedTheSame()
    {
        /** @var Mirror $service */
        $service = $this->pimple[\SomeThingElse::class];

        static::assertSame('@Parameter do not need such escaping@', $service->getConstructorArgs()[2]);
    }
}
