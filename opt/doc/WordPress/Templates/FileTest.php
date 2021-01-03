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
 * @copyright 2021 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Templates;

use Pimple\Container;
use RmpUp\WpDi\Provider;

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
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class FileTest extends TemplatesTestCase
{
    protected function setUp()
    {
        $this->pimple = new Container();
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
     */
    public function testRegisteredAsService(array $services)
    {
        (new Provider())($services, $this->pimple);

        static::assertEquals(
            'my-own-plugin/template-parts/fester.php',
            $this->pimple['%my-own-plugin/template-parts/fester.php%']
        );
    }

    /**
     * @dataProvider getDefinition
     */
    public function testFileExists($services)
    {
        (new Provider())($services, $this->pimple);

        $this->stubTemplateFile('my-own-plugin/template-parts/fester.php');

        $current = $this->pimple['%my-own-plugin/template-parts/fester.php%'];

        static::assertTemplatePathCorrect('my-own-plugin/template-parts/fester.php', $current);
    }

    protected function tearDown()
    {
        if (shortcode_exists('uncle')) {
            remove_shortcode('uncle');
        }

        parent::tearDown();
    }
}
