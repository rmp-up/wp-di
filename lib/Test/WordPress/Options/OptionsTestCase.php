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
 * https://mike-pretzlaw.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@mike-pretzlaw.de so we can mail you a copy.
 *
 * @package    wp-di
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-di
 * @since      2019-06-09
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test\WordPress\Options;

use RmpUp\WpDi\Helper\WordPress\OptionsResolver;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Provider\WordPress\Options;
use RmpUp\WpDi\Test\AbstractTestCase;
use RmpUp\WpDi\Test\ProviderTestCase;
use RmpUp\WpDi\Test\Sanitizer\SanitizerTestCase;

/**
 * OptionsTestCase
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-09
 */
abstract class OptionsTestCase extends ProviderTestCase
{
    /**
     * @var OptionsResolver
     */
    protected $optionsResolver;

    protected $optionsDefinition = [];

    protected function setUp()
    {
        parent::setUp();

        $this->optionsDefinition = [
            'foo' => 'bar',
            'baz' => static function () {
                return 'qux';
            },
            'ref' => static function ($container) {
                return $container['foo'];
            },
            'invalid' => static function ($container) {
                return $container[uniqid('', true)];
            }
        ];

        foreach ($this->optionsDefinition as $key => $value) {
            $this->pimple[$key] = $value;
        }

        $this->optionsResolver = new OptionsResolver($this->container);
        $this->pimple->register(new Provider([Options::class => $this->optionsDefinition]));
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