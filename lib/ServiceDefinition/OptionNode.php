<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OptionNode.php
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

namespace RmpUp\WpDi\ServiceDefinition;

/**
 * OptionNode
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class OptionNode extends AbstractNode
{
    /**
     * @see get_option()
     */
    const DEFAULT = false;
    /**
     * @var mixed
     */
    private $default;

    /**
     * @var string
     */
    private $optionName;

    public function __construct(string $optionName, $default = self::DEFAULT)
    {
        $this->optionName = $optionName;
        $this->default = $default;
    }

    public function __invoke()
    {
        return get_option($this->optionName, $this->wakeup($this->default));
    }
}