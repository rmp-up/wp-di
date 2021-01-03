<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ParserNode.php
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

namespace RmpUp\WpDi\Provider;

use Pimple\Container;

/**
 * ParserNode
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
trait ProviderNodeTrait
{
    /**
     * @var ProviderNode[][]
     */
    protected $nodes = [];

    public function addProvider($key, ProviderNode $node) {
        if (false === array_key_exists($key, $this->nodes)) {
            $this->nodes[$key] = [];
        }

        $this->nodes[$key][] = $node;
    }

    /**
     * @param array      $definition
     * @param Container  $pimple
     * @param string|int $key
     */
    public function __invoke(array $definition, Container $pimple, $key = '')
    {
        foreach (array_intersect_key($this->nodes, $definition) as $section => $extensions) {
            // Found keywords will be forwarded to their handler.

            // @phpstan-ignore-next-line The extension may be a string sometimes.
            if (false === is_array($extensions)) {
                $extensions = [$extensions];
            }

            foreach ($extensions as $extension) {
                // Handler receive the specific config but also the general container for further processing.
                $extension($definition[$section], $pimple, $section);
            }
        }
    }
}