<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpPostType.php
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
 * @since      2019-05-28
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RmpUp\WpDi\Helper\WordPress\RegisterPostType;

/**
 * WpPostType
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-05-28
 */
class WpPostTypes extends Services
{
    const KEY = 'wp_post_types';
    /**
     * @var array
     */
    private $definitions;

    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $psr = new \Pimple\Psr11\Container($pimple);

        foreach ($this->definitions as $serviceName => $definition) {
            parent::compile($pimple, $serviceName, $definition);

            if (array_key_exists(WpPostTypes::KEY, $definition)) {
                add_action('init', new RegisterPostType($psr, $serviceName, $definition[WpPostTypes::KEY]));
            }
        }
    }
}