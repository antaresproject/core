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

namespace Antares\Support\Traits\TestCase;

use Mockery as m;
use Antares\Support\Traits\ObservableTrait;

class ObservableTraitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Support\Traits\ObservableTrait::$dispatcher.
     *
     * @test
     */
    public function testEventDispatcher()
    {
        $dispatcher = m::mock('\Illuminate\Events\Dispatcher');

        $this->assertNull(ObservableStub::getEventDispatcher());

        ObservableStub::setEventDispatcher($dispatcher);

        $this->assertEquals($dispatcher, ObservableStub::getEventDispatcher());

        ObservableStub::unsetEventDispatcher();

        $this->assertNull(ObservableStub::getEventDispatcher());
    }

    /**
     * Test Antares\Support\Traits\ObservableTrait::getObservableEvents()
     * method.
     *
     * @test
     */
    public function testGetObservableEventsMethod()
    {
        $stub1 = new ObservableStub();
        $stub2 = new ObservableStubWithoutEvents();

        $this->assertEquals(['saving', 'saved'], $stub1->getObservableEvents());
        $this->assertEquals([], $stub2->getObservableEvents());
    }

    /**
     * Test Antares\Support\Traits\ObservableTrait::observe()
     * method without event dispatcher.
     *
     * @test
     */
    public function testObserveWithoutDispatcher()
    {
        ObservableStub::flushEventListeners();

        ObservableStub::observe(new FoobarObserver());

        $stub = new ObservableStub();
        $stub->save();

        $this->assertFalse($stub->saving);
        $this->assertFalse($stub->saved);
    }

    /**
     * Test Antares\Support\Traits\ObservableTrait::observe()
     * method with event dispatcher.
     *
     * @test
     */
    public function testObserveWithDispatcher()
    {
        $dispatcher = m::mock('\Illuminate\Events\Dispatcher');

        $stub = new ObservableStub();

        $dispatcher->shouldReceive('listen')->once()
                ->with('saving: ' . __NAMESPACE__ . '\\ObservableStub', __NAMESPACE__ . '\\FoobarObserver@saving')
                ->shouldReceive('listen')->once()
                ->with('saved: ' . __NAMESPACE__ . '\\ObservableStub', __NAMESPACE__ . '\\FoobarObserver@saved')
                ->shouldReceive('fire')->once()
                ->with('saving: ' . __NAMESPACE__ . '\\ObservableStub', $stub)
                ->shouldReceive('fire')->once()
                ->with('saved: ' . __NAMESPACE__ . '\\ObservableStub', $stub)
                ->shouldReceive('forget')->once()
                ->with('saving: ' . __NAMESPACE__ . '\\ObservableStub')
                ->shouldReceive('forget')->once()
                ->with('saved: ' . __NAMESPACE__ . '\\ObservableStub');

        ObservableStub::setEventDispatcher($dispatcher);

        ObservableStub::observe(new FoobarObserver());

        $stub->save();

        $this->assertFalse($stub->saving);
        $this->assertFalse($stub->saved);

        ObservableStub::flushEventListeners();
    }

}

class ObservableStub
{

    use ObservableTrait;

    public $saving = false;
    public $saved = false;

    public function save()
    {
        $this->fireObservableEvent('saving', false);
        $this->fireObservableEvent('saved', false);
    }

    public function getObservableEvents()
    {
        return ['saving', 'saved'];
    }

}

class ObservableStubWithoutEvents
{

    use ObservableTrait;
}

class FoobarObserver
{

    public function saving($stub)
    {
        $stub->saving = true;
    }

    public function saved($stub)
    {
        $stub->saving = true;
    }

}
