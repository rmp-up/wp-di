<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RegisterMetaBoxTest.php
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
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Arrays\ArrayBracketSpacingSniff;
use RmpUp\WpDi\Test\WordPress\MetaBoxTestCase;

/**
 * Register Meta-Box
 *
 * A minimal configuration would be to specify a screen for the meta-box
 * so that it only appears for one single post-type:
 *
 * ```yaml
 * services:
 *   MyBox:
 *     meta_box:
 *       screen: page
 * ```
 *
 * Everything else will be the default options as known from `add_meta_box()`.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class RegisterMetaBoxTest extends MetaBoxTestCase
{
    protected function compatSetUp()
    {
        parent::compatSetUp();

        global $wp_meta_boxes;
        $this->assertMetaBoxNotExists(MyBox::class, 'page');
    }

    public function testRegistersMetaBox()
    {
        global $wp_meta_boxes;
        $this->assertMetaBoxNotExists(MyBox::class, 'page');

        $this->registerServices();
        do_action('add_meta_boxes_page');

        $this->assertMetaBoxExists(MyBox::class, 'page');
    }

    protected function compatTearDown()
    {
        remove_meta_box(MyBox::class, 'page', 'advanced');

        parent::compatTearDown();
    }
}
