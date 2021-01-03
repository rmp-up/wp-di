<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ServicesTest.php
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
 * @copyright 2021 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-di
 */

declare(strict_types=1);

namespace RmpUp\WpDi\Test;

use RmpUp\WpDi\Helper\LazyPimple;

/**
 * Services
 *
 * As usual services are loaded only when needed from the container.
 * This way they spare some costs (ticks, memory, reads/writes, network, etc.)
 * and can be exchanged as long as not yet loaded.
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class ServicesTest extends AbstractTestCase
{
    /**
     * @var LazyPimple
     */
    private $lazy;

    protected function setUp()
    {
        parent::setUp();

        $this->pimple['qux'] = static function () {
            return new Mirror();
        };

        $this->lazy = new LazyPimple($this->pimple, 'qux');
    }

    public function testLoadsServiceWhenAsked()
    {
        $this->assertServiceNotLoaded('qux');

        $this->lazy->__invoke('test');

        $this->assertServiceLoaded('qux');
    }
}