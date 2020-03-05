<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Actions.php
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
 * @since      2019-06-12
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Sanitizer\WordPress;

use RmpUp\WpDi\Sanitizer\Services;
use RmpUp\WpDi\Provider\WordPress\Actions as Provider;

/**
 * Sanitize action definitions
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 * @deprecated 0.7.0 Use \RmpUp\WpDi\Compiler\Filter instead.
 */
class Actions extends Services
{
	public const DEFAULT_ARG_COUNT = 1;
	public const DEFAULT_PRIORITY = 10;

	public function sanitize($node): array
	{
		foreach ($node as $actionName => $hooks) {
			foreach ($hooks as $hookId => $hook) {
				if (!is_array($hook)) {
					// Possibly simple class or service.
					$hook = [
						Provider::SERVICE => [$hookId => $hook],
						Provider::PRIORITY => self::DEFAULT_PRIORITY,
						Provider::ARG_COUNT => self::DEFAULT_ARG_COUNT,
					];
				}

				if (is_array($hook) && !array_key_exists(Provider::SERVICE, $hook)) {
					$hook = [
						Provider::SERVICE => [$hookId => $hook],
						Provider::PRIORITY => self::DEFAULT_PRIORITY,
						Provider::ARG_COUNT => self::DEFAULT_ARG_COUNT,
					];
				}

				$hook[Provider::SERVICE] = parent::sanitize((array) $hook[Provider::SERVICE]);

				$node[$actionName][$hookId] = $hook;
			}
		}

		return $node;
	}
}