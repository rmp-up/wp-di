<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ConstantsTest.php
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

use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * Using constants as value
 *
 * At some point you may like to access a PHP constant
 * or use a WordPress constant.
 * Usually this would be:
 *
 * ```yaml
 * templates:
 *
 *   envemo.jpg: !join [ !php/const WP_CONTENT_DIR, "/plugins/my-own/public/vw.jpg" ]
 *   # result: .../wp-content/plugins/my-own/public/vw.jpg
 *
 *   envemo2.jpg: !join [ !php/const ABSPATH, !php/const WPINC, "/images/smilies/icon_eek.gif" ]
 *   # result: .../wp-includes/images/smilies/icon_eek.gif
 * ```
 *
 * The template parts looks up if one of the images exists
 * and returns the first match.
 * With the `!php/const` part it will prepend the current value of the
 * WP_CONTENT_DIR-constant
 * and append the remaining string.
 * Each part is separated by a empty space which makes it possible to
 * concat multiple constants (here: `ABSPATH` and `WPINC`).
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class ConstantsTest extends AbstractTestCase
{
    protected function compatSetUp()
    {
        parent::compatSetUp();

        $this->registerServices();
    }

    public function testConstantAndOneString()
    {
        static::assertEquals(
            WP_CONTENT_DIR . '/plugins/my-own/public/vw.jpg',
            $this->pimple['%envemo.jpg%']
        );
    }

    public function testMultipleConstantsAndStrings()
    {
        static::assertEquals(
            ABSPATH . WPINC . '/images/smilies/icon_eek.gif',
            $this->pimple['%envemo2.jpg%']
        );
    }
}
