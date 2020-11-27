<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TemplatesTestCase.php
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
 * @copyright 2020 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Templates;

use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Sanitizer\WordPress\Templates;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * TemplatesTestCase
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
abstract class TemplatesTestCase extends SanitizerTestCase
{
    protected $definition;

    protected $stubFiles = [];

    public static function assertTemplateExists(string $fileName)
    {
        static::assertFileExists(ABSPATH . WPINC . '/theme-compat/' . trim($fileName, '/'));
    }

    public static function assertTemplatePathCorrect(string $expectedWithoutWpinc, string $current)
    {
        static::assertEquals(ABSPATH . WPINC . '/theme-compat/' . trim($expectedWithoutWpinc, '/'), $current);
    }

    protected function stubTemplateFile(string $fileName): string
    {
        $fullPath = ABSPATH . '/wp-includes/theme-compat/' . trim($fileName, '/');
        $dir = dirname($fullPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        touch($fullPath);

        $this->stubFiles[] = $fullPath;

        return realpath($fullPath);
    }

    protected function tearDown()
    {
        foreach ($this->stubFiles as $stubFile) {
            unlink($stubFile);
        }

        parent::tearDown();
    }
}
