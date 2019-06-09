<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * UseInServiceTest.php
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
use RmpUp\WpDi\Test\WordPress\Options\OptionsTestCase;

/**
 * Using options in services
 *
 * Options can be used like parameters:
 *
 * ```php
 * return [
 *   WordPress\Options::class => [
 *     'blog_public',
 *     'ping_sites',
 *   ],
 *
 *   WpActions::class => [
 *     'publish_report' => [
 *       TellOtherAboutNewReport::class => [
 *         'blog_public',
 *         'ping_sites',
 *       ],
 *     ]
 *   ]
 * ]
 * ```
 *
 * So we injected the options via constructor
 * instead of writing `get_option( 'ping_sites' )` somewhere in the class body.
 * Now it can make use of `$this->pingSites` which is easier to test and maintain.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-09
 */
class UseInServiceTest extends OptionsTestCase
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

        $this->mockFilter('pre_option_blog_public')->expects($this->any())->willReturn(0);
        $this->mockFilter('pre_option_ping_sites')->expects($this->any())->willReturn(['example.org', 'rmp-up.de']);
    }

    public function testInjectOptions()
    {
        do_action(IncludesTest::class);

        /** @var Mirror $mirror */
        $mirror = $this->pimple[Mirror::class];

        static::assertEquals(
            [0, ['example.org', 'rmp-up.de']],
            $mirror->getConstructorArgs()
        );
    }

    public function testInjectOptionsDefault()
    {
        /** @var Mirror $mirror */
        $mirror = $this->pimple['service_with_default'];

        static::assertEquals(['D-FAULT'], $mirror->getConstructorArgs());
    }

    public function testInjectExistingOptionsInsteadOfDefault()
    {
        $this->mockFilter('pre_option_with_default')->expects($this->once())->willReturn('Ta Hun Kwai');
        /** @var Mirror $mirror */
        $mirror = $this->pimple['service_with_default'];

        static::assertEquals(['Ta Hun Kwai'], $mirror->getConstructorArgs());
    }

    public function tearDown()
    {
        remove_all_filters('pre_option_blog_public');
        remove_all_filters('pre_option_ping_sites');
        remove_all_filters('pre_option_with_default');

        parent::tearDown();
    }
}