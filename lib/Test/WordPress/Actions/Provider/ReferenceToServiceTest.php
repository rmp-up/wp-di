<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ReferenceToServiceTest.php
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
 * @since      2019-06-11
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Actions\Provider;

use PHPUnit\Framework\Constraint\IsEqual;
use Pretzlaw\WPInt\Filter\FilterAssertions;
use RmpUp\WpDi\LazyService;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\Mirror;
use RmpUp\WpDi\Test\WordPress\Actions\AbstractWpActionsTest;

/**
 * ReferenceToServiceTest
 *
 * @internal
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-11
 */
class ReferenceToServiceTest extends AbstractWpActionsTest
{
    use FilterAssertions;

    protected function setUp()
    {
        parent::setUp();

        $this->pimple->register(
            new Provider(
                [
                    Provider\Services::class => [
                        'test_service' => Mirror::class,
                    ],

                    Provider\WpActions::class => [
                        'test_action' => [
                            'test_service',
                        ]
                    ],
                ]
            )
        );
    }

    public function testAddsService()
    {
        static::assertFilterHasCallback(
            'test_action',
            new IsEqual(
                new LazyService($this->container, 'test_service')
            )
        );
    }

    public function testForwardsToService()
    {
        $expected = 'Phantom protocol';

        // see \RmpUp\WpDi\Test\Mirror::__invoke
        $mirror = apply_filters('test_action', $expected);

        static::assertEquals([$expected], $mirror['invoked']);
    }

    protected function tearDown()
    {
        remove_all_filters('test_action');

        parent::tearDown();
    }
}