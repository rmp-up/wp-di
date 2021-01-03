<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MetaBoxTestCase.php
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

namespace RmpUp\WpDi\Test\WordPress;

use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * Meta-Boxes
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
abstract class MetaBoxTestCase extends AbstractTestCase
{
    private $backupScreen;

    protected function setUp()
    {
        parent::setUp();

        // Imagine we are on the post screen
        global $current_screen;
        $this->backupScreen = $current_screen;
        $current_screen = convert_to_screen('post');
    }

    protected function findMetaBox(string $id, $postType = null, $context = null, $priority = null)
    {
        global $wp_meta_boxes;

        if (empty($wp_meta_boxes)) {
            return null;
        }

        $registeredBoxes = $wp_meta_boxes;

        if (null !== $postType) {
            // Reduce to defined post type
            $registeredBoxes = [$postType => $wp_meta_boxes[$postType] ?? []];
        }

        foreach ($registeredBoxes as $contexts) {
            if (null !== $context) {
                // Reduce to defined post type
                $contexts = [$context => $contexts[$context] ?? []];
            }

            foreach ($contexts as $currentContext => $priorities) {
                if (null !== $priority) {
                    // Reduce to the queried priority
                    $priorities = [$priority => $priorities[$priority] ?? []];
                }

                foreach ($priorities as $boxes) {
                    if (array_key_exists($id, $boxes) && false !== $boxes[$id]) {
                        return $boxes[$id];
                    }
                }
            }
        }

        return null;
    }

    protected function assertMetaBoxExists(string $id, $postType = null, $context = null)
    {
        static::assertNotNull($this->findMetaBox($id, $postType, $context), sprintf('Meta box "%s" does not exist', $id));
    }

    protected function assertMetaBoxNotExists(string $id, $postType = null, $context = null)
    {
        static::assertNull($this->findMetaBox($id, $postType, $context), sprintf('Meta box "%s" does exist', $id));
    }

    protected function tearDown()
    {
        global $current_screen;
        $current_screen = $this->backupScreen;

        parent::tearDown();
    }
}