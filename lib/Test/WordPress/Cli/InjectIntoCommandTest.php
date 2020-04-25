<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * InjectIntoCommandTest.php
 *
 * LICENSE: This source file is created by the company around M. Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package   wp-di
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

namespace RmpUp\WpDi\Test\WordPress\Cli;

require_once __DIR__ . '/SomeCliCommandHere.php';

use SomeCliCommandHere;
use RmpUp\WpDi\LazyService;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\Mirror;
use RmpUp\WpDi\Test\ProviderTestCase;
use SomeThing;
use WP_CLI;

/**
 * Inject into CLI commands
 *
 * When there are things needed for a cli command then you
 * need to register a service first.
 * The CLI command then just points to that service.
 *
 * ```php
 * <?php
 *
 * use RmpUp\WpDi\Provider\WordPress;
 * use RmpUp\WpDi\Provider\Services;
 *
 * return [
 *   Services::class => [
 *     // The actual service
 *     'cli_migrate_party' => [
 *       'class' => SomeCliCommandHere::class,
 *       'arguments' => [
 *         // Other service gets injected here
 *         SomeThing::class
 *       ]
 *     ],
 *
 *     SomeThing::class => [ 'robbie', 'meyers' ],
 *   ],
 *
 *   WordPress\CliCommands::class => [
 *     'beer 13' => 'cli_migrate_party'
 *   ]
 * ];
 * ```
 *
 * This way an instance of `SomeThing` is injected in `SomeCliCommandHere`
 * which is stored as the "cli_migrate_party" service.
 * For WP-CLI we just reference to this service.
 * So running `wp beer 13` will execute `SomeCliCommandHere::__invoke`.
 *
 * Note: When registering things for WP-CLI they won't be lazy anymore!
 * When WordPress is used from console then a full instance of the service is
 * provided to wp-cli due to its way of extracting the documentation.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class InjectIntoCommandTest extends ProviderTestCase
{
    protected function setUp()
    {
        $this->provider = new Provider($this->classComment()->execute(0));

        parent::setUp();

        $this->provider->register($this->pimple);
    }

    public function testFoo()
    {
        /** @var SomeCliCommandHere $cliCommand */
        $cliCommand = $this->container->get('cli_migrate_party');

        $injectedVar = $cliCommand->getConstructorArgs()[0];
        static::assertInstanceOf(SomeThing::class, $injectedVar);

        static::assertNotEmpty(WP_CLI::_history('add_command'), 'Add command has not been called');

        /** @var LazyService|Mirror $lazyAdded */
        $lazyAdded = WP_CLI::_history('add_command')[0]['arguments'][1];
        static::assertInstanceOf(LazyService::class, $lazyAdded);

        $this->assertLazyService('cli_migrate_party', $lazyAdded);

        static::assertInstanceOf('SomeThing', $lazyAdded->getConstructorArgs()[0], 'SomeThing class has not been injected');
        static::assertSame($injectedVar, $lazyAdded->getConstructorArgs()[0]);
    }

    public function testForwardsConfig()
    {
        $history = \WP_CLI::_history('add_command');
        $recent = end($history);

        static::assertArrayHasKey(2, $recent['arguments'], 'No config provided');

        $config = $recent['arguments'][2];
        static::assertSame('Prints a greeting.', $config['shortdesc']);
        static::assertContains('## EXAMPLES', $config['longdesc']);
        static::assertContains('    wp example hello Jerry', $config['longdesc']);
    }
}