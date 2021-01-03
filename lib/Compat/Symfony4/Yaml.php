<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Yaml.php
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

namespace RmpUp\WpDi\Compat\Symfony4;

use RmpUp\WpDi\Compat\YamlCommon;

/**
 * Parsing and dumping Yaml files
 *
 * Same as the Symfony Yaml component but pre-configured
 * to make the things known from the documentation work.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class Yaml extends YamlCommon
{
    public static function parse(string $input, int $flags = 0)
    {
        return self::process(
            parent::parse($input, $flags | static::$defaultFlags)
        );
    }

    public static function parseFile(string $filename, int $flags = 0)
    {
        return self::process(
            parent::parseFile($filename, $flags | static::$defaultFlags)
        );
    }
}