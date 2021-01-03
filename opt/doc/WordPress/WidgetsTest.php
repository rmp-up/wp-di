<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WidgetsTest.php
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

namespace RmpUp\WpDi\Test\WordPress;

use MyOwnWidget;
use Pretzlaw\WPInt\Mocks\Widget;
use ReflectionFunction;
use RmpUp\WpDi\Compiler\Widgets;
use RmpUp\WpDi\Provider;
use RmpUp\WpDi\Test\AbstractTestCase;
use RuntimeException;

/**
 * Widgets
 *
 * Registering new widgets in WordPress can be done
 * using the `register_widgets()` function.
 * The first and only argument of this function
 * should be an instance of WP_Widget (or child-) class.
 *
 * ```yaml
 * services:
 *   MyOwnWidget:
 *     widgets: ~
 * ```
 *
 * Just like this a new widget has been registered.
 *
 * Note: Due to WordPress limitations Widgets will never be lazy.
 * So please keep your constructor clean and simple
 * to provide an amazing performance.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class WidgetsTest extends AbstractTestCase
{
    protected function tearDown()
    {
        if (isset($this->pimple[MyOwnWidget::class]) && $this->pimple->raw(MyOwnWidget::class)['class']) {
            unregister_widget($this->pimple[MyOwnWidget::class]);
            static::assertWidgetNotExists('rmpup_myownwidget');
        }

        parent::tearDown();
    }

    public function testDefinition()
    {
        static::assertTrue(true);
    }

    public function testExampleRegistersWidget()
    {
        static::assertWidgetNotExists('rmpup_myownwidget');
        (new Provider())($this->yaml(), $this->pimple);
        static::assertWidgetExists('rmpup_myownwidget');
    }

    /**
     * @group doc
     * @internal
     */
    public function testRegisterWidgetHasJustOneArgument()
    {
        static::assertContains('first and only argument', $this->classComment()->markdown());

        $func = new ReflectionFunction('register_widget');
        static::assertEquals(1, $func->getNumberOfParameters());
    }

    public function testExceptionWhenClassNameIsWrong()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No class name provided for widget');

        $this->pimple->offsetSet(MyOwnWidget::class, [
                'class' => false,
                'widgets' => null,
            ]
        );

        (new Widgets())->__invoke(null, MyOwnWidget::class, $this->pimple);
    }
}