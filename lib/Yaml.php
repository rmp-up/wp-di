<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * YAML compatibility switch
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

namespace RmpUp\WpDi;

use RmpUp\WpDi\Helper\Versions;
use RmpUp\WpDi\Compat\Symfony3\Yaml as Symfony3Yaml;
use RmpUp\WpDi\Compat\Symfony4\Yaml as Symfony4Yaml;

switch (true) {
    case Versions::isLowerThan('symfony/yaml', '4.0.0'):
        class_alias(Symfony3Yaml::class, Yaml::class);
        break;
    default:
        class_alias(Symfony4Yaml::class, Yaml::class);
}
