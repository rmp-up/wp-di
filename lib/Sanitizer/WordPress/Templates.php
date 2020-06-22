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
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package   WpDi
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt proprietary
 * @link      https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Sanitizer\WordPress;

use Closure;
use RmpUp\WpDi\Sanitizer\SanitizerInterface;

/**
 * Templates
 *
 * When you write a plugin or (parent-)theme
 * then you surely like to keep it extensible.
 * WordPress has created `locate_template` for this
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Templates implements SanitizerInterface
{
    /**
     * Turn service definitions in service definitions
     *
     * @param array $node Mostly abbreviated service definitions.
     *
     * @return array
     */
    public function sanitize($node): array
    {
        $sanitized = [];

        foreach ($node as $serviceName => $templates) {
            if (is_int($serviceName) && is_string($templates)) {
                $serviceName = $templates;
            }

            if (false === $templates instanceof Closure) {
                $templates = (array) $templates;
            }

            $sanitized[$serviceName] = $templates;
        }

        return $sanitized;
    }
}
