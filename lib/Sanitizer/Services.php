<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ArraySanitizer.php
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

namespace RmpUp\WpDi\Sanitizer;

use RmpUp\WpDi\Provider\Services as ServicesProvider;

/**
 * ArraySanitizer
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Services implements SanitizerInterface
{
    /**
     * @deprecated 1.0.0 Use Provider\Services::CLASS_NAME instead.
     */
    const CLASS_NAME = ServicesProvider::CLASS_NAME;

    /**
     * @deprecated 1.0.0 Use Provider\Services::ARGUMENTS instead.
     */
    const ARGUMENTS = ServicesProvider::ARGUMENTS;

    /**
     * @param array $node
     * @return array
     */
    public function sanitize($node): array
    {
        if ([] === $node) {
            // Do not consume memory for nothing.
            return $node;
        }

        $sanitized = [];
        foreach ($node as $id => $definition) {
            if (is_string($definition)) {
                if (is_int($id)) {
                    $id = $definition;
                }

                $definition = [
                    ServicesProvider::CLASS_NAME => $definition,
                    ServicesProvider::ARGUMENTS => [],
                ];
            }

            if (is_array($definition) && !array_key_exists(ServicesProvider::CLASS_NAME, $definition)) {
                $definition[ServicesProvider::CLASS_NAME] = $id;
            }

            $sanitized[$id] = $definition;
        }

        return $sanitized;
    }
}