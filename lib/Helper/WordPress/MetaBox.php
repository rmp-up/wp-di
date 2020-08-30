<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MetaBox.php
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

namespace RmpUp\WpDi\Helper\WordPress;

/**
 * MetaBox
 *
 * This class could have become a Closure but closures can not be serialized
 * so the backup-capability of PHPUnit would break.
 * To prevent this we introduce a dedicated class.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class MetaBox
{
    /**
     * @var callable
     */
    private $callback;
    /**
     * @var string
     */
    private $context;
    private $id;
    /**
     * @var string
     */
    private $priority;
    /**
     * @var null
     */
    private $screen;
    private $title;

    public function __construct($id, $title, $callback, $screen = null, $context = null, $priority = null)
    {
        if (null === $context) {
            $context = 'advanced';
        }

        if (null === $priority) {
            $priority = 'default';
        }

        $this->id = $id;
        $this->title = $title;
        $this->callback = $callback;
        $this->screen = $screen;
        $this->context = $context;
        $this->priority = $priority;
    }

    public function __invoke()
    {
        add_meta_box(
            $this->id,
            $this->title,
            $this->callback,
            $this->screen,
            $this->context,
            $this->priority
        );
    }
}