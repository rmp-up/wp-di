<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Widgets.php
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
use RuntimeException;

/**
 * Widgets
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Widgets implements CompilerInterface
{
    public function __invoke($definition, string $serviceName, Container $pimple)
    {
        $className = $pimple->raw($serviceName)['class'] ?? $serviceName;
        if (!$className) {
            throw new RuntimeException('No class name provided for widget (see' . $serviceName . ')');
        }

        register_widget($pimple[$serviceName]);
    }
}