<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OptionsTest.php
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
 * @since      2019-06-08
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress;

use RmpUp\WpDi\Sanitizer\WordPress\Options;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * Options
 *
 * Options can be manipulated or loaded from other services using the options-provider:
 *
 * ```php
 * <?php
 *
 * use \RmpUp\WpDi\Provider\WordPress;
 *
 * return [
 *   WordPress\Options::class => [
 *     'my_fav_isni' => 423379498,
 *   ]
 * ];
 * ```
 *
 * Speaking of WordPress 4.7.0 (and above) this service will only be triggered
 * when the option is not set.
 * Once the option is set (e.g. in the database / via `set_option`)
 * the service won't be used.
 * This again saves some runtime for huge callbacks
 * or references as shown below.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-08
 */
class OptionsTest extends AbstractTestCase
{
    public function testExists()
    {
        static::assertTrue(class_exists(Options::class));
        static::assertTrue(class_exists(\RmpUp\WpDi\Provider\WordPress\Options::class));
    }
}