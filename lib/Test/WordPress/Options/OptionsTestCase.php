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
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * OptionsTestCase
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-06-09
 */
abstract class OptionsTestCase extends AbstractTestCase
{
    /**
     * @var OptionsResolver
     */
    protected $optionsResolver;

    protected function setUp()
    {
        parent::setUp();

        $this->pimple->offsetSet('foo', 'bar');

        $this->pimple->offsetSet('baz', static function () {
            return 'qux';
        });

        $this->pimple->offsetSet('ref', static function ($container) {
            return $container['foo'];
        });

        $this->optionsResolver = new OptionsResolver($this->container);
    }

    protected function option($optionName, $default = null)
    {
        return $this->optionsResolver->__invoke($default, $optionName, func_num_args() > 1);
    }

}