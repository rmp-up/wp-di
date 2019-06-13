<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ActionsTest.php
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
 * @since      2019-05-30
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress;

use RmpUp\WpDi;

/**
 * Actions
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-05-30
 */
class ActionsTest extends WpDi\Test\AbstractTestCase
{
    public function testBasicsExist()
    {
        static::assertTrue(class_exists(WpDi\Provider\WordPress\Actions::class));
        static::assertTrue(class_exists(WpDi\Sanitizer\WordPress\Actions::class));

        static::assertTrue(class_exists(WpDi\Provider\WpActions::class));
        static::assertTrue(class_exists(WpDi\Sanitizer\WpActions::class));
    }
}