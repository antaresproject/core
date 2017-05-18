<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
 namespace Antares\Memory\Abstractable\TestCase;

use Mockery as m;
use Antares\Memory\ContainerTrait;

class ContainerTraitTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTrait;

    /**
     * Test multiple functionality of Antares\Memory\Abstractable\Container.
     *
     * @test
     */
    public function testAttachingMemoryProviders()
    {
        $mock = m::mock('\Antares\Memory\Provider');

        $this->assertFalse($this->attached());

        $this->attach($mock);

        $this->assertEquals($mock, $this->memory);
        $this->assertEquals($mock, $this->getMemoryProvider());
        $this->assertTrue($this->attached());
    }
}
