<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * KeepProperDefinitionTest.php
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
 * @since      2019-04-29
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Cli;

use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Sanitizer\WpCliCommands;
use RmpUp\WpDi\Test\Mirror;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * Adding commands
 *
 * Defining commands can be done with an associative array:
 *
 * ```php
 * <?php
 *
 * return [
 *   Provider\WpCliCommands::class => [
 *     'some thing' => SomeCommand::class,
 *     'foo'        => Another::class,
 *   ]
 * ]
 * ```
 *
 * When running wp-cli it will have the two new commands `some thing`
 * and `foo` which invokes the defined classes/services the same way as wp-cli does
 * (e.g. via `__invoke`).
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-04-29
 */
class ShortSyntaxTest extends SanitizerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->sanitizer = new WpCliCommands();
    }

    /**
     */
    public function testExtendShortSpecialConfig()
    {
        static::assertEquals(
            [
                // First arg
                Mirror::class => [
                    Services::CLASS_NAME => Mirror::class,
                    Services::ARGUMENTS => [],
                    \RmpUp\WpDi\Provider\WpCliCommands::KEY => [
                        \RmpUp\WpDi\Provider\WpCliCommands::COMMAND => 'some command'
                    ]
                ]
            ],
            $this->sanitizer->sanitize([
                'some command' => Mirror::class
            ])
        );
    }
}