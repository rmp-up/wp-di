<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Deprecated.php
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

namespace RmpUp\WpDi\Helper;

use Pimple\Container;

/**
 * Mark services or API signatures as deprecated
 *
 * This class can be used as an object to help marking a service
 * or service definition as deprecated.
 * It triggers the error for deprecations
 * which can be switched on/off by using the related environment variables.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class Deprecated
{
    /**
     * @var string
     */
    private $message;
    private $serviceDefinition;

    public function __construct($serviceDefinition, string $message)
    {
        $this->serviceDefinition = $serviceDefinition;
        $this->message = $message;
    }

    public function __invoke(Container $pimple)
    {
        trigger_error($this->makeDeprecatedMessage(), E_USER_DEPRECATED);

        if ($this->serviceDefinition instanceof \Closure) {
            return ($this->serviceDefinition)($pimple);
        }

        return $this->serviceDefinition;
    }

    /**
     * Inform about forward-compatible changes
     *
     * Forward-compatible changes are easier to resolve
     * because wp-di will support the new interface
     * and the old interface.
     * This simplifies the changes that are needed to upgrade to
     * the next major version,
     * which are "marked" as forward-compatible when using this method.
     *
     * To PHP this still is an E_USER_DEPRECATED-error just like in
     * forward-incompatible changes.
     * But forward-compatible changes will always raise an error during testing,
     * which you can't get rid of until the new API/interface is fulfilled.
     * Errors about forward-incompatible changes can be suppressed
     *
     * @param string $message
     */
    public static function forwardCompatible(string $message)
    {
        if (false === (bool) getenv('WPDI_ERROR_DEPRECATED')) {
            return;
        }

        trigger_error($message, E_USER_DEPRECATED);
    }

    /**
     * Warn about forward incompatibility changes
     *
     * We try to bypass forward incompatible changes
     * into forward compatible so that updating to a major version hurts less.
     * Sometimes it is not possible to create a full compatibility
     * with the next major version.
     * We warn developer about this by using this method.
     *
     * Warning developers about incompatible changes is done using
     * `trigger_error` and checked via PHPUnit
     * (using the "convertDeprecationsToExceptions"-option).
     * Unfortunately the above mentioned unresolvable incompatible changes
     * will also raise an exception which is okay to inform developers
     * but not for our internal usage leading to failing CI-Tests.
     * So we suppress those deprecation warnings
     * when the "WPDI_TEST" environment is set to one, true or similar.
     *
     * @param string $message
     */
    public static function forwardIncompatible(string $message)
    {
        if (false === (bool) getenv('WPDI_ERROR_FORWARD_INCOMPATIBLE')) {
            return;
        }

        trigger_error($message, E_USER_DEPRECATED);
    }

    private function makeDeprecatedMessage(): string
    {
        $message = $this->message;

        if (is_object($this->serviceDefinition)) {
            $message = get_class($this->serviceDefinition) . ': ' . $message;
        }

        return $message;
    }
}