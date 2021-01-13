<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DeprecatedTest.php
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

use ArrayObject;
use phpmock\MockBuilder;
use RmpUp\WpDi\Helper\Deprecated;
use RmpUp\WpDi\Test\AbstractTestCase;

/**
 * DeprecatedTest
 *
 * @covers \RmpUp\WpDi\Helper\Deprecated
 *
 * @internal
 */
class DeprecatedTest extends AbstractTestCase
{
	/**
	 * @var \phpmock\Mock
	 */
	private $triggerErrorMock;

	/**
	 * @var array
	 */
	private $triggerErrorStack = [];

	protected function assertHasDeprecationMessage($message)
	{
		static::assertEquals(
			[
				[
					'message' => $message,
					'level' => E_USER_DEPRECATED,
				]
			],
			$this->triggerErrorStack
		);
	}

	protected function compatSetUp()
	{
		parent::compatSetUp();

		putenv('WPDI_ERROR_FORWARD_INCOMPATIBLE=1');

		$this->enableTriggerErrorMock();

	}

	protected function compatTearDown()
	{
		putenv('WPDI_ERROR_FORWARD_INCOMPATIBLE=0');

		$this->triggerErrorMock->disable();

		$this->triggerErrorStack = [];

		parent::compatTearDown();
	}

	protected function enableTriggerErrorMock()
	{
		if (null === $this->triggerErrorMock) {
			// Mock trigger_error
			$this->triggerErrorMock = (new MockBuilder())
				->setNamespace('\\RmpUp\\WpDi\\Helper')
				->setName('trigger_error')
				->setFunction(function ($message, $logLevel) {
					$this->triggerErrorStack[] = [
						'message' => $message,
						'level' => $logLevel,
					];
				})
				->build();
		}

		$this->triggerErrorMock->enable();
	}

	public function testHelpsRaisingForwardCompatibleDeprecationError()
	{
		$expectedMessage = uniqid('', true);

		Deprecated::forwardCompatible($expectedMessage);

		$this->assertHasDeprecationMessage($expectedMessage);
	}

	public function testHelpsRaisingForwardIncompatibleDeprecationError()
	{
		$expectedMessage = uniqid('', true);

		Deprecated::forwardIncompatible($expectedMessage);

		$this->assertHasDeprecationMessage($expectedMessage);
	}

	public function testTriggersDeprecationError()
	{
		static::assertEmpty($this->triggerErrorStack);

		(new Deprecated(new ArrayObject(), 'funny gal'))->__invoke($this->pimple);

		$this->assertHasDeprecationMessage('ArrayObject: funny gal');
	}

	public function testNoForwardIncompatibleErrorWhenSuppressed()
	{
		putenv('WPDI_ERROR_FORWARD_INCOMPATIBLE=0');

		Deprecated::forwardIncompatible(uniqid('', true));

		static::assertEmpty($this->triggerErrorStack);
	}
}
