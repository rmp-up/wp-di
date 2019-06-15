<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Templates.php
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
 * @package   WpDi
 * @copyright 2019 Mike Pretzlaw
 * @license   https://mike-pretzlaw.de/license-generic.txt proprietary
 * @link      https://project.mike-pretzlaw.de/wp-di
 * @since     2019-06-15
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Provider\WordPress;

use Closure;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Templates
 *
 * @copyright 2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since     2019-06-15
 */
class Templates implements ServiceProviderInterface
{
    /**
     * Mapping of service name to array of possible templates
     *
     * @var array
     */
    private $definition;

    /**
     * Template provider
     *
     * @param array $definition Mapping of service name to array of possible templates.
     */
    public function __construct(array $definition)
    {
        $this->definition = $definition;
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple): void
    {
        foreach ($this->definition as $serviceName => $templates) {
            if ($templates instanceof Closure) {
                // Already a function so we reuse this instead.
                $pimple[$serviceName] = $templates;
                continue;
            }

            $pimple[$serviceName] = static function () use ($templates) {
                $templatePath = '';

                foreach ($templates as $templatePath) {
                    $located = locate_template($templatePath, false, false);

                    if ('' !== $located) {
                        return $located;
                    }

                    if (file_exists($templatePath)) {
                        return $templatePath;
                    }
                }

                // In doubt return the very last entry
                return $templatePath;
            };
        }
    }
}
