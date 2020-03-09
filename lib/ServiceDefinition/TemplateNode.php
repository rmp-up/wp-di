<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TemplateNode.php
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

namespace RmpUp\WpDi\ServiceDefinition;

/**
 * TemplateNode
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class TemplateNode
{
    /**
     * All possible locations
     *
     * @var array
     */
    private $templates;

    /**
     * Caching to spare costs.
     * @var string
     */
    private $resolvedPath;

    public function __construct(array $templates)
    {
        $this->templates = $templates;
    }

    public function __invoke(): string
    {
        if (null === $this->resolvedPath) {
            $this->resolve();
        }

        return $this->resolvedPath;
    }

    private function resolve()
    {
        $templatePath = '';
        foreach ($this->templates as $templatePath) {
            $located = locate_template($templatePath, false, false);

            if ('' !== $located) {
                $this->resolvedPath = $located;
                return;
            }

            if (file_exists($templatePath)) {
                $this->resolvedPath = $templatePath;
                return;
            }
        }

        // In doubt use the very last entry
        $this->resolvedPath = $templatePath;
    }
}