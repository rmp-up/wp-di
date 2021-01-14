<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * YamlTest.php
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

namespace RmpUp\WpDi\Test\Helper;

use RmpUp\WpDi\Compiler\Yaml\Join;
use RmpUp\WpDi\Helper\Yaml\Implode;
use RmpUp\WpDi\Test\AbstractTestCase;
use Symfony\Component\Yaml\Tag\TaggedValue;

/**
 * YamlTest
 *
 * @covers \RmpUp\WpDi\Compiler\Yaml\Join::__invoke
 */
class YamlTest extends AbstractTestCase
{
	/**
	 * @internal
	 */
	public function testSymfonyCompat()
	{
		$taggeValue = new TaggedValue('join', [
				'    Hello',
				'    World',
		]);

		$join = new Join();

		/** @var Implode $implode */
		$implode = $join->__invoke($taggeValue);

		static::assertEquals('    Hello    World', $implode());
	}
}
