<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Implode.php
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

namespace RmpUp\WpDi\Helper\Yaml;

use RmpUp\WpDi\Helper\LazyInvoke;

/**
 * Implode
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Implode implements LazyInvoke
{
    private $parts;
    /**
     * @var string
     */
    private $separator;

    public function __construct(string $seperator, $parts)
    {
        $this->parts = $parts;
        $this->separator = $seperator;
    }

    public function __invoke()
    {
        $parsedParts = [];
        foreach ($this->parts as $part) {
            if ($part instanceof LazyInvoke) {
                $part = $part();
            }

            $parsedParts[] = $part;
        }

        return implode($this->separator, $parsedParts);
    }
}