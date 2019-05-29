<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpActionsProvider.php
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
 * @since      2019-04-25
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Provider;

use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
use RmpUp\WpDi\LazyService;

/**
 * WpActionsProvider
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-04-25
 */
class WpActions extends Services
{
    const KEY = 'actions';
    const SERVICE = 'service';
    const PRIORITY = 'priority';
    const ARG_COUNT = 'arg_count';

    public function register(Container $pimple)
    {
        $psr = new PsrContainer($pimple);

        foreach ($this->services as $event => $hooks) {
            foreach ($hooks as $definition) {
                if (!array_key_exists(self::SERVICE, $definition) || !is_array($definition[self::SERVICE])) {
                    throw new MissingServiceDefinitionException('Invalid hook definition: Missing service');
                }

                $serviceName = key($definition[self::SERVICE]);

                if (!$serviceName) {
                    throw new InvalidActionDefinitionException('Invalid action definition');
                }

                if (!$psr->has($serviceName)) {
                    $this->compile($pimple, $serviceName, reset($definition[self::SERVICE]));
                }

                $this->registerAction(
                    (string)$event,
                    $psr,
                    (string)$serviceName,
                    (int)$definition[self::PRIORITY],
                    (int)$definition[self::ARG_COUNT]
                );
            }
        }
    }

    /**
     * @param $event
     * @param PsrContainer $container
     * @param $serviceName
     * @param int $priority
     * @param int $argCount
     * @return true|void
     */
    protected function registerAction(
        string $event,
        PsrContainer $container,
        string $serviceName,
        int $priority,
        int $argCount
    )
    {
        return add_action(
            $event,
            new LazyService($container, $serviceName),
            $priority,
            $argCount
        );
    }
}