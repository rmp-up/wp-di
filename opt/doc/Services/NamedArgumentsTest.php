<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * NamedArgumentsTest.php
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

namespace RmpUp\WpDi\Test\Services;

use RmpUp\WpDi\Test\AbstractTestCase;
use RmpUp\WpDi\Test\Mirror;
use SomeThing;

/**
 * NamedArgumentsTest
 *
 * ```yaml
 * services:
 *   SomeThing:
 *     arguments:
 *       first: reunited
 *       and: it
 *       feels: good
 * ```
 *
 * @internal
 */
class NamedArgumentsTest extends AbstractTestCase
{
	protected function compatSetUp()
	{
		parent::compatSetUp();

		$this->registerServices();
	}

	public function testNamedArgumentsAreInjected()
	{
		/** @var Mirror $mirror */
		$mirror = $this->pimple[SomeThing::class];

		static::assertEquals(
			[
				'reunited',
				'it',
				'good'
			],
			$mirror->getConstructorArgs()
		);
	}
}
