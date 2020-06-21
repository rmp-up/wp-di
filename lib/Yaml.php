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

namespace RmpUp\WpDi;

use RmpUp\WpDi\Compiler\Yaml\Join;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;

/**
 * Parsing and dumping Yaml files
 *
 * Same as the Symfony Yaml component but pre-configured
 * to make the things known from the documentation work.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Yaml extends SymfonyYaml
{
    private static $defaultFlags =
        SymfonyYaml::PARSE_CONSTANT
        | SymfonyYaml::PARSE_CUSTOM_TAGS;

    public static $tags = [
        'join' => Join::class,
    ];

    public static function parse(string $input, int $flags = 0)
    {
        return self::process(
            parent::parse($input, $flags | static::$defaultFlags)
        );
    }

    protected static function parseRecursive(array &$data)
    {
        foreach ($data as &$datum) {
            $datum = static::convertTags($datum);

            if (is_array($datum)) {
                static::parseRecursive($datum);
            }
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
    private static function process($data)
    {
        if (is_array($data)) {
            static::parseRecursive($data);
        }

        if ($data instanceof TaggedValue) {
            $data = static::convertTags($data);
        }

        return $data;
    }

    public static function parseFile(string $filename, int $flags = 0)
    {
        return self::process(
            parent::parseFile($filename, $flags | static::$defaultFlags)
        );
    }

    public static function convertTags($value)
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