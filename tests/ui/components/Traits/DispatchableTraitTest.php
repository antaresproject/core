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
 * @package    Widgets
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Widgets\Traits\Tests;

use Antares\Widgets\Traits\DispatchableTrait as Stub;
use Illuminate\Support\Collection;
use Illuminate\Events\Dispatcher;
use Antares\Testing\TestCase;
use Mockery as m;

class DispatchableTraitTest extends TestCase
{

    use Stub;

    /**
     * @var array | collection 
     */
    protected $widgets;

    /**
     * @var Dispatcher 
     */
    protected $dispatcher;

    /**
     * test Antares\Widgets\Traits\DispatchableTrait::booted()
     * 
     * @test
     */
    public function testBooted()
    {
        $this->assertFalse($this->booted());
    }

    /**
     * test Antares\Widgets\Traits\DispatchableTrait::finish()
     * 
     * @test
     */
    public function testFinish()
    {
        $this->widgets    = ['foo' => ['name' => 'foo']];
        $dispatcher       = m::mock(Dispatcher::class);
        $dispatcher->shouldReceive('finish')
                ->with('foo', ['name' => 'foo'])
                ->andReturnNull();
        $this->dispatcher = $dispatcher;
        $this->finish();
        $this->assertInstanceOf(Collection::class, $this->widgets);
    }

}
