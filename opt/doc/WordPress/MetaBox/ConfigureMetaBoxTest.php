<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ConfigureMetaBoxTest.php
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

use RmpUp\WpDi\Test\WordPress\MetaBoxTestCase;

/**
 * Configure Meta-Box
 *
 * A complete configuration would have a title,
 * the screen(s) where it can appear,
 * context and priority:
 *
 * ```yaml
 * services:
 *   MyBox:
 *     meta_box:
 *       rlcs:
 *         title: Recent matches
 *         screen: post
 *         context: side
 *         priority: low
 * ```
 *
 * The ID is taken from the key (here: "rlcs").
 * This way you could have the same box with different titles,
 * context or priorities spread over some post-types.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class ConfigureMetaBoxTest extends MetaBoxTestCase
{
    public function testCanConfigureMetaBox()
    {
        $this->assertMetaBoxNotExists('rlcs', 'post');

        $this->registerServices();
        do_action('add_meta_boxes_post');

        $this->assertMetaBoxExists('rlcs', 'post');

        $registeredBox = $this->findMetaBox('rlcs', 'post', 'side', 'low');
        static::assertNotNull($registeredBox, 'Box not properly registered');

        static::assertEquals('Recent matches', $registeredBox['title']);

    }

    protected function tearDown()
    {
        parent::tearDown();

        remove_meta_box('rlcs', 'post', 'side');
    }
}