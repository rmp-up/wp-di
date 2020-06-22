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

namespace RmpUp\WpDi\Test\WordPress\Templates\Definition;

use Pimple\Container;
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
 * ```yaml
 * templates:
 *   some-feature.php:
 *     - template-parts/my-plugin/some-feature.php
 *     - other-plugin/template-parts/some-feature.php
 *     - my-plugin/template-parts/some-feature.php
 *
 * services:
 *   SomeThing:
 *     arguments: [ 'some-feature.php' ]
 * ```
 *
 * Now we have a service named "some-feature.php" that tries each file
 * (using `locate_template`) and stops with the first found.
 * In doubt it returns the very last entry even when not found.
 *
 * In PHP it can be defined like this:
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
 *       'other-plugin/template-parts/some-feature.php',
 *       'my-plugin/template-parts/some-feature.php',
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
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class MultipleFilesTest extends TemplatesTestCase
{
    protected function setUp()
    {
        $this->pimple = new Container();
        $this->sanitizer = new Templates();

        // Disabled default setup
    }

    public function getDefinitions(): array
    {
        return [
            'php' => [
                $this->classComment()->execute(1),
                \RmpUp\WpDi\Provider\WordPress\Templates::class
            ],
            'yaml' => [
                $this->yaml(0),
                'templates'
            ]
        ];
    }

    /**
     * @param $services
     *
     * @dataProvider getDefinitions
     */
    public function testExtendsToArray($services, $index)
    {
        $templates = [
            'template-parts/my-plugin/some-feature.php',
            'other-plugin/template-parts/some-feature.php',
            'my-plugin/template-parts/some-feature.php',
        ];

        static::assertEquals(
            [
                'some-feature.php' => $templates,
                '%some-feature.php%' => $templates,
            ],
            $this->sanitizer->sanitize($services[$index])
        );
    }

    /**
     * @dataProvider getDefinitions
     */
    public function testRegisteredAsService($definition)
    {
        $this->pimple->register(new Provider($definition));

        static::assertEquals('my-plugin/template-parts/some-feature.php', $this->pimple['some-feature.php']);
    }

    /**
     * @dataProvider getDefinitions
     */
    public function testSecondOneExists($config)
    {
        $this->pimple->register(new Provider($config));

        $fullPath = $this->stubTemplateFile('other-plugin/template-parts/some-feature.php');

        self::assertNotEmpty($fullPath);
        self::assertEquals($fullPath, $this->pimple['some-feature.php']);
    }
}
