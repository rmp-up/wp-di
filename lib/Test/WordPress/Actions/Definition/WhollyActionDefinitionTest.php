<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * KeepProperDefinitionsTest.php
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
 * @since      2019-04-27
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Actions\Definition;

use RmpUp\WpDi\Provider\WordPress\Actions as Provider;
use RmpUp\WpDi\Sanitizer\WordPress\Actions as Sanitizer;
use RmpUp\WpDi\Test\Mirror;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * Internal definition
 *
 *
 * @internal
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-04-27
 */
class WhollyActionDefinitionTest extends SanitizerTestCase
{

    protected function setUp()
    {
        $this->sanitizer = new Sanitizer();
    }

    public function definitions(): array
    {
        return [
            [
                // Service reference
                [
                    'actionName' => [
                        Mirror::class => [
	                        Provider::SERVICE   => [
                                Mirror::class => [
	                                Provider::CLASS_NAME => Mirror::class,
	                                Provider::ARGUMENTS  => [],
                                ]
                            ],
	                        Provider::PRIORITY  => 10,
	                        Provider::ARG_COUNT => 1,
                        ]
                    ]
                ],
            ],
            [
                // New service
                [
                    'actionName' => [
                        Mirror::class => [
	                        Provider::SERVICE   => [
                                Mirror::class => [
	                                Provider::CLASS_NAME => Mirror::class,
	                                Provider::ARGUMENTS  => [
                                        42,
                                        1337
                                    ],
                                ],
                            ],
	                        Provider::PRIORITY  => 10,
	                        Provider::ARG_COUNT => 1,
                        ]
                    ]
                ],
            ]
        ];
    }

    /**
     * @dataProvider definitions
     * @param $actions
     */
    public function testKeepDefinitions($actions)
    {
        static::assertEquals($actions, $this->sanitizer->sanitize($actions));
    }
}