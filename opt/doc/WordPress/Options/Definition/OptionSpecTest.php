<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OptionSpecTest.php
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

namespace RmpUp\WpDi\Test\WordPress\Options\Definition;

use RmpUp\WpDi\Sanitizer\WordPress\Options;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * Full option spec
 *
 * @internal
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class OptionSpecTest extends SanitizerTestCase
{
    private $spec = [
        'some_option' => 'some_default_value',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->sanitizer = new Options();
    }

    public function testDoesNotChangeSpec()
    {
        static::assertEquals($this->spec, $this->sanitizer->sanitize($this->spec));
    }
}