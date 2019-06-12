<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RegisterPostType.php
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
 * @since      2019-05-29
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Helper\WordPress;

use Pimple\Psr11\Container;
use Psr\Container\ContainerInterface;

/**
 * RegisterPostType
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-05-29
 */
class RegisterPostType
{
    /**
     * @var Container
     */
    private $container;
    private $serviceName;
    private $postType;

    public function __construct(ContainerInterface $container, $serviceName, $postType)
    {
        $this->container = $container;
        $this->serviceName = $serviceName;
        $this->postType = $postType;
    }

    public function __invoke()
    {
        $postType = $this->container->get($this->serviceName);

        if (is_callable($postType) || method_exists($postType, '__invoke')) {
            $postType($this->postType);
            return;
        }

        register_post_type($this->postType, get_object_vars($postType));
    }
}