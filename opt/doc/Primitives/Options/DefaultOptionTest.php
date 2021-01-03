<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DefaultOptionTest.php
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

namespace RmpUp\WpDi\Test\Primitives\Options;

use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * Default option
 *
 * When an option does not exist in WordPress then you may want to define a default
 * (without writing it to the database directly).
 *
 * ```yaml
 * options:
 *   stellar_location: outer space
 *   stellar_time: 03:20:00
 * ```
 *
 * Now every call for `get_option('stellar_time')` would return the default
 * value "03:20:00" if the option is not yet set (in the database).
 * Even when the option is retrieved from other plugins/themes.
 *
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class DefaultOptionTest extends AbstractTestCase
{
    private $describedDefault;
    private $describedOption;

    protected function setUp()
    {
        parent::setUp();

        $this->registerServices();

        $description = $this->classComment()->xpath('//p')[1]->asXML();

        strtok($description, '"');
        $this->describedDefault = strtok('"');
        static::assertNotEmpty($this->describedDefault);

        strtok($description, "'");
        $this->describedOption = strtok("'");
        static::assertEquals('stellar_time', $this->describedOption, 'Description changed, please adapt test');

    }

    public function testGetOptionReturnsDefault()
    {
        static::assertSame($this->describedDefault, get_option($this->describedOption));
    }
}