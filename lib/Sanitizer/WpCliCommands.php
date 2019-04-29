<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpCliCommands.php
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

namespace RmpUp\WpDi\Sanitizer;

/**
 * WpCliCommands
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-04-29
 */
class WpCliCommands extends Services
{
    public function sanitize($node): array
    {
        $sanitized = [];

        foreach ($node as $serviceName => $serviceDefinition) {
            $service = parent::sanitize([$serviceName => $serviceDefinition]);

            if (is_string($serviceDefinition)) {
                $service[$serviceName][\RmpUp\WpDi\Provider\WpCliCommands::KEY] = [
                    \RmpUp\WpDi\Provider\WpCliCommands::COMMAND => $serviceName,
                ];

                $serviceDefinition = $service[$serviceName];
                $serviceName = $serviceDefinition[\RmpUp\WpDi\Provider\Services::CLASS_NAME];
            }

            $sanitized[$serviceName] = $serviceDefinition;
        }

        return $sanitized;
    }
}