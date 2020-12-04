<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Factory.php
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

use RmpUp\WpDi\Compiler\Filter;
use RmpUp\WpDi\Compiler\MetaBox;
use RmpUp\WpDi\Compiler\PostType;
use RmpUp\WpDi\Compiler\Shortcode;
use RmpUp\WpDi\Compiler\Widgets;
use RmpUp\WpDi\Compiler\WpCli;
use RmpUp\WpDi\Provider\Parameters;
use RmpUp\WpDi\Provider\Services;
use RmpUp\WpDi\Provider\WordPress\Options;
use RmpUp\WpDi\Provider\WordPress\Templates;

/**
 * Factory
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Factory
{
    /**
     * @return Filter
     */
    protected static function createFilterNode(): Filter
    {
        return new Filter();
    }

    /**
     * @return MetaBox
     */
    protected static function createMetaBoxNode(): MetaBox
    {
        return new MetaBox();
    }

    /**
     * @return Options
     */
    public static function createOptionsProvider(): Options
    {
        return new Options();
    }

    /**
     * @return Parameters
     */
    public static function createParametersProvider(): Parameters
    {
        return new Parameters();
    }

    /**
     * @return PostType
     */
    protected static function createPostTypeNode(): PostType
    {
        return new PostType();
    }

    public static function createProvider(): Provider
    {
        return new Provider(self::getProviderArguments());
    }

    /**
     * @return Shortcode
     */
    protected static function createShortcodeNode(): Shortcode
    {
        return new Shortcode();
    }

    /**
     * @return Widgets
     */
    protected static function createWidgetsNode(): Widgets
    {
        return new Widgets();
    }

    /**
     * @return WpCli
     */
    protected static function createWpCliNode(): WpCli
    {
        return new WpCli();
    }

    public static function getProviderArguments(): array
    {
        $arguments = [
            'services' => self::createServicesProvider(),
            'options' => self::createOptionsProvider(),
            'parameters' => self::createParametersProvider(),
            'templates' => self::createTemplatesProvider(),
        ];

        $arguments[Services::class] = $arguments['services'];
        $arguments[Options::class] = $arguments['options'];
        $arguments[Parameters::class] = $arguments['parameters'];
        $arguments[Templates::class] = $arguments['templates'];

        return $arguments;
    }

    /**
     * @return Services
     */
    public static function createServicesProvider(): Services
    {
        return new Services(self::getServiceProviderArguments());
    }

    /**
     * @return Templates
     */
    public static function createTemplatesProvider(): Templates
    {
        return new Templates();
    }

    public static function getServiceProviderArguments(): array
    {
        $filter = [self::createFilterNode()];
        $shortcodes = [self::createShortcodeNode()];

        return [
            Filter::FILTER_KEY => $filter,
            'add_filter' => $filter, // alias

            Filter::ACTION_KEY => $filter,
            'add_action' => $filter, // alias

            'meta_box' => [self::createMetaBoxNode()],

            'post_type' => [self::createPostTypeNode()],

            Shortcode::KEY => $shortcodes,
            'add_shortcode' => $shortcodes,

            'widgets' => [self::createWidgetsNode()],
            'wp_cli' => [self::createWpCliNode()],
        ];
    }
}