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

namespace RmpUp\WpDi\Compat;

use RmpUp\WpDi\Compiler\Yaml\Join;
use RmpUp\WpDi\Compiler\Yaml\Translate;
use RmpUp\WpDi\Compiler\Yaml\YamlCompiler;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symfony\Component\Yaml\Yaml as SymfonyYaml;

/**
 * Yaml
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
abstract class YamlCommon extends SymfonyYaml
{
    protected static $defaultFlags =
        SymfonyYaml::PARSE_CONSTANT
        | SymfonyYaml::PARSE_CUSTOM_TAGS;

    /**
     * @var array<string,string|YamlCompiler>
     */
    public static $tags = [
        'join' => Join::class,
        
        // Translations
        '__' => Translate::class,
        '_n' => Translate::class,
        '_nx' => Translate::class,
        '_x' => Translate::class,
        'esc_attr__' => Translate::class,
        'esc_attr_x' => Translate::class,
        'esc_html__' => Translate::class,
        'esc_html_x' => Translate::class,
    ];

    /**
     * @var YamlCompiler[]
     */
    private static $instances = [];

    /**
     * @param string $compiler
     *
     * @return YamlCompiler
     */
    private static function instantiate(string $compiler): YamlCompiler
    {
        if (false === array_key_exists($compiler, self::$instances)) {
            self::$instances[$compiler] = new $compiler;
        }

        return self::$instances[$compiler];
    }

    private static function parseRecursive(array &$data)
    {
        foreach ($data as &$datum) {
            if (is_array($datum)) {
                self::parseRecursive($datum);
                continue;
            }

            $datum = self::convertTags($datum);
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
            self::parseRecursive($data);
        }

        if ($data instanceof TaggedValue) {
            $data = self::convertTags($data);
        }

        return $data;
    }

    private static function convertTags($value)
    {
        if (false === $value instanceof TaggedValue || false === array_key_exists($value->getTag(), static::$tags)) {
            return $value;
        }

        // Parse inner tags before parsing the outer
        $parsedValue = [];
        foreach ((array) $value->getValue() as $single) {
            $parsedValue[] = self::convertTags($single);
        }

        $tag = $value->getTag();
        $compiler = static::$tags[$tag];

        if (is_string($compiler)) {
            $compiler = static::$tags[$tag] = self::instantiate($compiler);
        }

        return $compiler(new TaggedValue($tag, $parsedValue));
    }
}