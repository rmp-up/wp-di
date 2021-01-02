<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AddAsDefaultOptionTest.php
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
 * @package    wp-di
 * @copyright 2020 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Options\Provider;

use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Provider\WordPress\Options;
use RmpUp\WpDi\Test\WordPress\Options\OptionsTestCase;

/**
 *
 * Default options
 *
 * As shown default options can be easily added like this:
 *
 * ```yaml
 * options:
 *   my_fav_isni: 423379498
 * ```
 *
 * Such configuration will make the `get_option( 'my_fav_isni' )` return the
 * integer 423379498.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class DefaultOptionTest extends OptionsTestCase
{
    public function testUseDefaultOption()
    {
        static::assertFalse(get_option('my_fav_isni'));

        $this->pimple->register(new Provider($this->yaml(0)));

        $optionValue = get_option('my_fav_isni');

        static::assertEquals(423379498, $optionValue);
    }

    /**
     * Override the default with a default
     *
     * When using above configuration `get_option( 'my_fav_isni' )` will return the defined integer.
     * But as you may know `get_option` can have it's own default value as second parameter.
     * The resolver will notice when `get_option( 'my_fav_isni', 114766152 )` is used
     * and prefer this custom default.
     */
    public function testOverrideDefaultOption()
    {
        static::assertEquals(114766152, get_option('my_fav_isni', 114766152));
    }

    /**
     * Option as callback
     *
     * Options provided as callback will be invoked before they are returned.
     * This is useful to build an option by using other services:
     *
     * ```php
     * <?php
     *
     * return [
     *   Services::class => [
     *     'foo' => SomeRepository::class,
     *   ],
     *
     *   WordPress\Options::class => [
     *     'bar' => static function ($container) {
     *       return $container['foo']->findByName('bar');
     *     },
     *   ]
     * ```
     *
     * So in case the option is not set it will be fetched from an repository here.
     */
    public function testDefaultOptionAsCallback()
    {
        static::assertSame('qux', get_option('baz'));
    }

    /**
     * Referencing other options
     *
     * By now other options can be referenced using a similar callback as above:
     *
     * ```php
     * <?php
     *
     * return [
     *   WordPress\Options::class => [
     *     'key_entity_ii' => 'Holly Wood',
     *     'the_cracked' => static function ($container) {
     *       return $container['key_entity_ii'];
     *     },
     *
     *     'the_butcher' => static function ($container) {
     *       return $container['key_entity_iii'];
     *     },
     *   ]
     * ];
     * ```
     *
     * In this example `get_option( 'key_entitiy_ii' )` has the same
     * default value as `get_option( 'the_cracked' )`.
     *
     * Maybe you know that PSR-11 like to throw an exception when an service does not exist.
     * This is the case for the second reference where the "key_entity_iii" is not defined.
     * But we don't want your application to fail on this one
     * so `get_option( 'the_butcher' )` will not raise an exception
     * and just leave it to WordPress what the default value ist.
     */
    public function testDefaultOptionAsReference()
    {
        // Existing reference
        static::assertEquals('bar', get_option('ref'));
        static::assertEquals('bar', get_option('ref'));

        static::assertFalse(get_option('invalid'));
        static::assertEquals('omnomnom', get_option('invalid', 'omnomnom'));

    }

    protected function tearDown()
    {
        remove_all_actions('default_option_my_fav_isni');

        parent::tearDown();
    }
}