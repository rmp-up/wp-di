<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Join.php
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

namespace RmpUp\WpDi\Compiler\Yaml;

use RmpUp\WpDi\Helper\Yaml\Implode;
use RmpUp\WpDi\Yaml;
use Symfony\Component\Yaml\Tag\TaggedValue;

/**
 * Join
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Join implements YamlCompiler
{
    public function __invoke(TaggedValue $taggedValue)
    {
        $value = [];

        foreach ((array) $taggedValue->getValue() as $line) {
            if (empty($line)) {
                // An empty line shall convert to a new line
                $value[] = "\n";
                continue;
            }

            if (is_string($line)) {
                if (' ' === $line[0]) {
                    // Need to swap out empty spaces, otherwise Symfony falsely nags about wrong indents.
                    $fullLength = strlen($line);
                    $line = ltrim($line, ' ');
                    $value[] = str_repeat(' ', $fullLength - strlen($line));
                }

                $line = Yaml::parse($line);
            }

            $value[] = $line;
        }

        return new Implode('', $value);
    }
}