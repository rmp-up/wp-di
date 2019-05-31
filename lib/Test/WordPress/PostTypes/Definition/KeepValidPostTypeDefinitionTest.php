<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * KeepValidPostTypeDefinitionTest.php
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
 * @since      2019-05-28
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\PostTypes\Definition;

use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Sanitizer\WpPostTypes;
use RmpUp\WpDi\Test\Mirror;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * KeepValidPostTypeDefinitionTest
 *
 * @internal
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-05-28
 */
class KeepValidPostTypeDefinitionTest extends SanitizerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->sanitizer = new WpPostTypes();
    }

    public function testKeepsCompletePostTypeDefinition()
    {
        $definition = [
            'post_type_name' => [
                Services::CLASS_NAME => Mirror::class,
                Services::ARGUMENTS => [],
                \RmpUp\WpDi\Provider\WpPostTypes::KEY => 'post_type_name',
            ]
        ];

        static::assertEquals($definition, $this->sanitizer->sanitize($definition));
    }
}