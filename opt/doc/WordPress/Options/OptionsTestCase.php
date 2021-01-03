<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OptionsTestCase.php
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
 * @copyright 2021 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Options;

use RmpUp\WpDi\Helper\WordPress\OptionsResolver;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Provider\WordPress\Options;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * Options
 *
 * ```php
 * <?php
 *
 * return [
 *   'foo' => 'bar',
 *   'baz' => static function () {
 *     return 'qux';
 *   },
 *   'ref' => static function ($container) {
 *     return $container['foo'];
 *   },
 *   'invalid' => static function ($container) {
 *     return $container[uniqid('', true)];
 *   }
 * ];
 * ```
 *
 * @internal
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
abstract class OptionsTestCase extends AbstractTestCase
{
    /**
     * @var OptionsResolver
     */
    protected $optionsResolver;

    protected $optionsDefinition = [];

    protected function setUp()
    {
        parent::setUp();

        $this->optionsDefinition = $this->classComment(OptionsTestCase::class)->code(0)->evaluate();

        foreach ($this->optionsDefinition as $key => $value) {
            $this->pimple[$key] = $value;
        }

        $this->optionsResolver = new OptionsResolver($this->pimple);

        (new Provider())([Options::class => $this->optionsDefinition], $this->pimple);
    }

    protected function option($optionName, $default = null)
    {
        return $this->optionsResolver->__invoke($default, $optionName, func_num_args() > 1);
    }

    protected function tearDown()
    {
        foreach (array_keys($this->optionsDefinition) as $key) {
            if (is_string($key)) {
                remove_all_actions('default_option_' . $key);
            }
        }

        parent::tearDown();
    }
}