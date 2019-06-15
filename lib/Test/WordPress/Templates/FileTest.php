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
use RmpUp\WpDi\Test\ProviderTestCase;
use RmpUp\WpDi\Test\WordPress\Templates\TemplatesTestCase;

/**
 * Single files
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
 *     'some-path/my-plugin-file.php'
 *   ],
 *
 *   Services::class => [
 *     SomeThing::class => [
 *       'some-path/my-plugin-file.php'
 *     ]
 *   ]
 * ];
 * ```
 *
 * This way you have a service "some-path/my-plugin-file.php"
 * which uses `locate_template` to resolve the path to the template.
 * The result will be injected into the SomeThing service/class.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-15
 */
class FileTest extends TemplatesTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->sanitizer = new Templates();

        $this->services = $this->classComment()->execute(0);
        $this->provider = new Provider($this->services);

        $this->pimple->register($this->provider);
    }

    public function testExtendsToArray()
    {
        static::assertEquals(
            [
                'some-path/my-plugin-file.php' => [
                    'some-path/my-plugin-file.php',
                ]
            ],
            $this->sanitizer->sanitize($this->services[\RmpUp\WpDi\Provider\WordPress\Templates::class])
        );
    }

    public function testRegisteredAsService()
    {
        static::assertEquals('some-path/my-plugin-file.php', $this->pimple['some-path/my-plugin-file.php']);
    }

    public function testFileExists()
    {
        $this->stubTemplateFile('some-path/my-plugin-file.php');

        $current = $this->pimple['some-path/my-plugin-file.php'];

        static::assertTemplatePathCorrect('some-path/my-plugin-file.php', $current);
    }
}
