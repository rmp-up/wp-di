<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * IncludesTest.php
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

namespace RmpUp\WpDi\Test\WordPress\Options\Provider;

use Pretzlaw\WPInt\Filter\FilterAssertions;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Sanitizer\WordPress\Options;
use RmpUp\WpDi\Test\Mirror;
use RmpUp\WpDi\Test\ProviderTestCase;

/**
 * Include an option
 *
 * To just use an option without a default value you need to add it as a simple entry:
 *
 * ```php
 * return [
 *   WordPress\Options::class => [
 *     'this_has_a' => 'default value',
 *     'blog_public',
 *     'admin_email',
 *   ]
 * ]
 * ```
 *
 * Compared to the defaults of an option the second
 * and the third just tell the service container that those options exist.
 * It is now aware that it shall load "blog_public"
 * and "admin_email" from the options table (via `get_options`).
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-09
 */
class IncludesTest extends ProviderTestCase
{
    use FilterAssertions;

    protected function setUp()
    {
        parent::setUp();

        $this->sanitizer = new Options();
        $this->provider = new Provider(
            [
                Provider\WpActions::class => [
                    IncludesTest::class => [
                        Mirror::class => [
                            'blog_public',
                            'ping_sites',
                        ],
                    ]
                ],

                Provider\Services::class => [
                    'service_with_default' => [
                        Provider\Services::CLASS_NAME => Mirror::class,
                        Provider\Services::ARGUMENTS => [
                            'with_default',
                        ]
                    ]
                ],

                Provider\WordPress\Options::class => [
                    'blog_public',
                    'ping_sites',
                    'with_default' => 'D-FAULT'
                ],
            ]
        );

        $this->pimple->register($this->provider);
    }

    public function testOptionsExistAsService()
    {
        static::assertEquals('D-FAULT', $this->pimple['with_default']);
        static::assertNotNull($this->pimple['blog_public']);
        static::assertNotNull($this->pimple['ping_sites']);
    }
}

