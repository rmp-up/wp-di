<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AsLazyServiceTest.php
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

namespace RmpUp\WpDi\Test\Provider\WpCli\Command;

use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Provider\WordPress\CliCommands;
use RmpUp\WpDi\Test\AbstractTestCase;
use RmpUp\WpDi\Test\Mirror;

/**
 * AsLazyServiceTest
 *
 * @internal
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-04-29
 */
class AsLazyServiceTest extends AbstractTestCase
{
    private const SERVICE_NAME = 'someService';
    /**
     * @var array
     */
    private $definitions;

    protected function setUp()
    {
        parent::setUp();

        $this->definitions = [
            self::SERVICE_NAME => [
                Services::CLASS_NAME => Mirror::class,
                Services::ARGUMENTS => [13],
                CliCommands::KEY => [
                    CliCommands::COMMAND => 'some command'
                ]
            ]
        ];

        $this->pimple->register(new CliCommands($this->definitions, Mirror::class));
    }

    public function testIsLazyService()
    {
        static::assertLazyService(self::SERVICE_NAME, Mirror::$staticCalls[0]['arguments'][1]);
    }

    public function testAddsCommand()
    {
        static::assertEquals('some command', Mirror::$staticCalls[0]['arguments'][0]);
    }
}