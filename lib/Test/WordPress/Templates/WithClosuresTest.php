<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WithClosuresTest.php
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
 * @since      2019-06-15
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Templates;

use RmpUp\WpDi\Provider\WordPress\Templates;
use RmpUp\WpDi\Test\ProviderTestCase;

/**
 * Custom resolver using callback
 *
 * When you need some custom implementation then it is still possible
 * to use anonymous functions:
 *
 * ```php
 * <?php
 *
 * use \RmpUp\WpDi\Provider\WordPress;
 *
 * return [
 *   WordPress\Templates::class => [
 *     'ncc-1717.php',
 *     'ncc-1701-a.php',
 *
 *     'my-footer.php' => static function ($container) {
 *       if ( 2286 < date( 'Y' ) ) {
 *         return $container['ncc-1717.php'];
 *       }
 *
 *       return $container['ncc-1701-a.php'];
 *     }
 *   ]
 * ];
 * ```
 *
 * Using a function enables you to switch templates at a specific time and date
 * or do other fancy stuff depending on the date-time,
 * the user,
 * the current post-type
 * and many more.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-15
 */
class WithClosuresTest extends TemplatesTestCase
{
    public function testClosureUnchanged()
    {
        static::assertSame(
            $this->definition[Templates::class]['my-footer.php'],
            $this->services[Templates::class]['my-footer.php']
        );
    }
}
