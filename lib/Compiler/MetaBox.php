<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MetaBox.php
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
 * @copyright 2021 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Compiler;

use Pimple\Container;
use RmpUp\WpDi\Helper\LazyPimple;

/**
 * MetaBox
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class MetaBox implements CompilerInterface
{
    public function __invoke($definition, string $serviceName, Container $pimple)
    {
        if (null === $definition || is_string(current($definition))) {
            // Flat definition or single meta_box will be normalized
            $definition = [$serviceName => (array) $definition];
        }

        foreach ($definition as $id => $boxConfig) {
            $screens = $boxConfig['screen'] ?? null;
            $metaBoxRegistration = new \RmpUp\WpDi\Helper\WordPress\MetaBox(
                $boxConfig['id'] ?? $id,
                $boxConfig['title'] ?? '',
                new LazyPimple($pimple, $serviceName),
                $screens,
                $boxConfig['context'] ?? null,
                $boxConfig['priority'] ?? null
            );

            if (null === $screens) {
                add_action('add_meta_boxes', $metaBoxRegistration);
            }

            foreach ((array) $screens as $item) {
                add_action('add_meta_boxes_' . $item, $metaBoxRegistration);
            }
        }
    }
}