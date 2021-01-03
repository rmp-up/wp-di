<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IncludesTest.php
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
 * @copyright 2021 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Options\Provider;

use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * Include an option
 *
 * To just use an option without a default value you need to add it as a simple entry:
 *
 * ```yaml
 * options:
 *   this_has_a: default value
 *   blog_public: ~
 *   admin_email: ~
 * ```
 *
 * Compared to the defaults of an option the second
 * and the third just tell the service container that those options exist.
 * It is now aware that it shall load "blog_public"
 * and "admin_email" from the options table (via `get_options`).
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class IncludesTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->registerServices();
    }

    public function testOptionsExistAsService()
    {
        static::assertEquals('default value', $this->pimple['%this_has_a%']);
        static::assertNotNull($this->pimple['%blog_public%']);
        static::assertNotNull($this->pimple['%admin_email%']);
    }
}

