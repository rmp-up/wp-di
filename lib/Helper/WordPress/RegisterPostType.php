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

use Pimple\Container;

/**
 * RegisterPostType
 *
 * @copyright  2020 Mike Pretzlaw (https://mike-pretzlaw.de)
 */
class RegisterPostType
{
    /**
     * @var Container
     */
    private $pimple;
    private $serviceName;
    private $postType;

    /**
     * RegisterPostType constructor.
     *
     * @param Container $pimple
     * @param string            $serviceName
     * @param string            $postType
     */
    public function __construct(Container $pimple, string $serviceName, string $postType)
    {
        $this->pimple = $pimple;
        $this->serviceName = $serviceName;
        $this->postType = $postType;
    }

    /**
     * @return string
     */
    public function getPostType(): string
    {
        return $this->postType;
    }

    public function __invoke()
    {
        $postTypeService = $this->pimple[$this->serviceName];

        if (is_callable($postTypeService) || method_exists($postTypeService, '__invoke')) {
            $postTypeService($this->postType);
            return;
        }

        register_post_type($this->postType, get_object_vars($postTypeService));
    }
}