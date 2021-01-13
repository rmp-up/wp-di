<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * NullConfigurationTest.php
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

namespace RmpUp\WpDi\Test\WordPress\MetaBox;

use MyBox;
use RmpUp\WpDi\Test\WordPress\MetaBoxTestCase;

/**
 * Configure Meta-Box
 *
 * The most minimal configuration would be to add a meta-box to each screen like this:
 *
 * ```yaml
 * services:
 *   MyBox:
 *     meta_box: ~
 * ```
 *
 * Such meta-box will have no title but should appear on each screen
 * in the advanced section.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class NullConfigurationTest extends MetaBoxTestCase
{
    public function testCanConfigureMetaBox()
    {
        $this->assertMetaBoxNotExists(MyBox::class, 'post');

        $this->registerServices();
        do_action('add_meta_boxes');

        $this->assertMetaBoxExists(MyBox::class, 'post');

        $registeredBox = $this->findMetaBox(MyBox::class, 'post');
        static::assertNotNull($registeredBox, 'Box not properly registered');

        static::assertEquals('', $registeredBox['title']);
        static::assertLazyPimple(MyBox::class, $registeredBox['callback']);
    }

    protected function compatTearDown()
    {
        parent::compatTearDown();

        remove_meta_box(MyBox::class, 'post', 'advanced');
    }
}
