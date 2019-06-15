<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * SingleFileTest.php
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

namespace RmpUp\WpDi\Test\WordPress\Templates\Definition;

use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Sanitizer\WordPress\Templates;
use const RmpUp\WpDi\Test\MY_PLUGIN_DIR;
use RmpUp\WpDi\Test\ProviderTestCase;
use RmpUp\WpDi\Test\WordPress\Templates\TemplatesTestCase;

/**
 * Resolve multiple files
 *
 * Most common way may be that you have a template file for front- or backend
 * in your plugin or theme.
 * To use it you can simply add it as a string:
 *
 * ```php
 * <?php
 *
 * use \RmpUp\WpDi\Provider\WordPress;
 * use \RmpUp\WpDi\Provider\Services;
 *
 * return [
 *   WordPress\Templates::class => [
 *
 *     'some-feature.php' => [
 *       'template-parts/my-plugin/some-feature.php',
 *       'my-plugin/template-parts/some-feature.php',
 *       MY_PLUGIN_DIR . '/template-parts/some-feature.php',
 *     ],
 *
 *   ],
 *
 *   Services::class => [
 *     SomeThing::class => [
 *       'some-feature.php'
 *     ]
 *   ]
 * ];
 * ```
 *
 * Now we have a service named "some-feature.php" that tries each file
 * (using `locate_template`) and stops with the first found.
 * In doubt it returns the very last entry even when not found.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-15
 */
class MultipleFilesTest extends TemplatesTestCase
{
    public function testExtendsToArray()
    {
        static::assertEquals(
            [
                'some-feature.php' => [
                    'template-parts/my-plugin/some-feature.php',
                    'my-plugin/template-parts/some-feature.php',
                    \MY_PLUGIN_DIR . '/template-parts/some-feature.php',
                ]
            ],
            $this->sanitizer->sanitize($this->services[\RmpUp\WpDi\Provider\WordPress\Templates::class])
        );
    }

    public function testRegisteredAsService()
    {
        static::assertEquals(\MY_PLUGIN_DIR . '/template-parts/some-feature.php', $this->pimple['some-feature.php']);
    }

    public function testSecondOneExists()
    {
        $this->stubTemplateFile('my-plugin/template-parts/some-feature.php');

        self::assertTemplatePathCorrect(
            'my-plugin/template-parts/some-feature.php',
            $this->pimple['some-feature.php']
        );
    }
}
