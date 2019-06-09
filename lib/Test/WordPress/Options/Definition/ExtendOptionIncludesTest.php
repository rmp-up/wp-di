<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ExtendOptionIncludes.php
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
 * @since      2019-06-09
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Options\Definition;

use Closure;
use RmpUp\WpDi\Sanitizer\WordPress\Options;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * ExtendOptionIncludes
 *
 * @internal
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-09
 */
class ExtendOptionIncludesTest extends SanitizerTestCase
{
    private $spec = [
        'include_this_option',
        'do_not_include_this' => 'because it has a default value',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->sanitizer = new Options();
    }

    public function testIncludeTurnsToCallback()
    {
        $sanitized = $this->sanitizer->sanitize($this->spec);

        static::assertArrayHasKey('include_this_option', $sanitized);
        static::assertInstanceOf(Closure::class, $sanitized['include_this_option']);
    }

    public function testNonIncludesRemain()
    {
        $sanitized = $this->sanitizer->sanitize($this->spec);

        static::assertArrayHasKey('do_not_include_this', $sanitized);
        static::assertEquals('because it has a default value', $sanitized['do_not_include_this']);
    }
}