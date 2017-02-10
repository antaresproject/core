<?php

/**
 * Part of the Antares Project package.
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
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Brands\TestCase;

use Mockery as m;
use Antares\Brands\Events\UserHandler as Stub;
use Antares\Testbench\TestCase;

class UserHandlerTest extends TestCase
{

    /**
     * @see parent::setUp
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * test constructing
     */
    public function testConstruct()
    {
        $stub = new Stub(m::mock('\Antares\Config\Repository'), m::mock('\Antares\Memory\MemoryManager'));
        $this->assertInstanceOf('Antares\Brands\Events\UserHandler', $stub);
    }

    /**
     * test onViewForm method
     */
    public function testOnViewform()
    {
        $stub        = new Stub(m::mock('\Antares\Config\Repository'), m::mock('\Antares\Memory\MemoryManager'));
        $user        = m::mock('\Antares\Model\User');
        $formBuilder = m::mock('\Antares\Html\Form\FormBuilder');
        $this->assertFalse($stub->onViewForm($user, $formBuilder));
    }

    /**
     * test onsaved method
     */
    public function testOnSaved()
    {
        $config = m::mock('\Antares\Config\Repository');
        $config->shouldReceive('get')->with(m::type('String'), false)->andReturn(true);

        $provider = m::mock('\Antares\Memory\Provider');
        $provider->shouldReceive('put')->once()->with(m::type('String'), NULL)->andReturnSelf();
        $memory   = m::mock('\Antares\Memory\MemoryManager');
        $memory->shouldReceive('make')->with(m::type('String'))
                ->andReturn($provider);


        $stub = new Stub($config, $memory);
        $user = m::mock('\Antares\Model\User');
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $this->assertNull($stub->onSaved($user));
    }

}
