<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PostType.php
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

declare(strict_types=1);

namespace RmpUp\WpDi\Compiler;

use Pimple\Container;
use RmpUp\WpDi\Helper\WordPress\RegisterPostType;

/**
 * PostType
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class PostType implements CompilerInterface
{
    public function __invoke($definition, string $serviceName, Container $pimple)
    {
        if (is_scalar($definition)) {
            $definition = [$definition => null];
        }

        foreach ($definition as $postType => $hook) {
            if (null === $hook) {
                $hook = ['init', 10];
            }

            if (is_scalar($hook)) {
                $hook = [$hook, 10];
            }

            add_action(
                $hook[0],
                new RegisterPostType($pimple, $serviceName, $postType),
                $hook[1],
                PHP_INT_MAX
            );
        }

    }
}