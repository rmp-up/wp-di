<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FileTest.php
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
 * @since      2019-06-15
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Templates\Definition;

use Pimple\Container;
use Prophecy\Doubler\Generator\Node\ArgumentNode;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Sanitizer\WordPress\Templates;
use RmpUp\WpDi\Test\ProviderTestCase;
use RmpUp\WpDi\Test\WordPress\Templates\TemplatesTestCase;

/**
 * Single files
 *
 * Most common way may be that you have a template file for front- or backend
 * in your plugin or theme.
 * To use it you can simply add
 * and use it like a parameter:
 *
 * ```yaml
 * templates:
 *   - my-own-plugin/template-parts/fester.php
 *   - my-own-plugin/public/coogan.jpg
 *
 * services:
 *   MyOwnShortcode:
 *     arguments:
 *       - my-own-plugin/template-parts/fester.php
 *       - my-own-plugin/public/coogan.jpg
 *     shortcode: uncle
 * ```
 *
 * As you can see the template does not necessarily need to be a PHP file
 * but can also be any other file.
 * This way you have a service "some-path/my-plugin-file.php"
 * which uses `locate_template` to resolve the path to the template.
 * The result will be injected into the SomeThing service/class.
 *
 * Defining this in PHP would look like this:
 *
 * ```php
 * <?php
 *
 * use \RmpUp\WpDi\Provider\WordPress;
 * use \RmpUp\WpDi\Provider\Services;
 *
 * return [
 *   WordPress\Templates::class => [
 *     'my-own-plugin/template-parts/fester.php',
 *     'my-own-plugin/public/coogan.jpg',
 *   ],
 *
 *   Services::class => [
 *     SomeThing::class => [
 *       '%my-own-plugin/template-parts/fester.php%',
 *       '%my-own-plugin/public/coogan.jpg%',
 *     ]
 *   ]
 * ];
 * ```
 *
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 * @since      2019-06-15
 */
class FileTest extends TemplatesTestCase
{
    protected function setUp()
    {
        $this->pimple = new Container();
        $this->pimple->register(new Provider($this->yaml(0)));
    }

    public function getDefinition(): array
    {
        return [
            '0.6' => [
                $this->classComment()->execute(1),
            ],
            '0.7' => [
                $this->yaml(0),
            ],
        ];
    }

    /**
     * @dataProvider getDefinition
     *
     * @param array $services
     */
    public function testExtendsToArray(array $services)
    {
        $this->pimple->register(new Provider($services));

        static::assertEquals(
            [
                'my-own-plugin/template-parts/fester.php' => [
                    'my-own-plugin/template-parts/fester.php',
                ],
                '%my-own-plugin/template-parts/fester.php%' => [
                    'my-own-plugin/template-parts/fester.php',
                ],
                'my-own-plugin/public/coogan.jpg' => [
                    'my-own-plugin/public/coogan.jpg',
                ],
                '%my-own-plugin/public/coogan.jpg%' => [
                    'my-own-plugin/public/coogan.jpg',
                ]
            ],
            (new Templates())->sanitize(
                $services[\RmpUp\WpDi\Provider\WordPress\Templates::class]
                ?? $services['templates']
            )
        );
    }

    /**
     * @dataProvider getDefinition
     */
    public function testRegisteredAsService(array $services)
    {
        $this->pimple->register(new Provider($services));

        static::assertEquals('my-own-plugin/template-parts/fester.php', $this->pimple['my-own-plugin/template-parts/fester.php']);
    }

    /**
     * @dataProvider getDefinition
     */
    public function testFileExists($services)
    {
        $this->pimple->register(new Provider($services));

        $this->stubTemplateFile('my-own-plugin/template-parts/fester.php');

        $current = $this->pimple['my-own-plugin/template-parts/fester.php'];

        static::assertTemplatePathCorrect('my-own-plugin/template-parts/fester.php', $current);
    }
}
