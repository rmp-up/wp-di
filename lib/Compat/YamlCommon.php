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
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Compat;

use RmpUp\WpDi\Compiler\Yaml\Join;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;

/**
 * Yaml
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
abstract class YamlCommon extends SymfonyYaml
{
    protected static $defaultFlags =
        SymfonyYaml::PARSE_CONSTANT
        | SymfonyYaml::PARSE_CUSTOM_TAGS;

    public static $tags = [
        'join' => Join::class,
    ];

    private static function parseRecursive(array &$data)
    {
        foreach ($data as &$datum) {
            if (is_array($datum)) {
                static::parseRecursive($datum);
                continue;
            }

            $datum = static::convertTags($datum);
        }
    }

    /**
     * Post-Process data
     *
     * Mostly to resolve custom tags.
     *
     * @param mixed $data
     *
     * @return array|TaggedValue
     */
    protected static function process($data)
    {
        if (is_array($data)) {
            static::parseRecursive($data);
        }

        if ($data instanceof TaggedValue) {
            $data = static::convertTags($data);
        }

        return $data;
    }

    private static function convertTags($value)
    {
        if (false === $value instanceof TaggedValue || false === array_key_exists($value->getTag(), static::$tags)) {
            return $value;
        }

        $tag = $value->getTag();
        $compiler = static::$tags[$tag];

        if (is_string($compiler) && class_exists($compiler)) {
            $compiler = static::$tags[$tag] = new $compiler;
        }

        return $compiler($value);
    }
}