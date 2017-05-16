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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Customfields\TestCase;

use Antares\Customfields\Events\FormHandler;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class FormHandlerTest extends ApplicationTestCase
{

    /**
     * @see parent::setUp
     */
    public function setUp()
    {
        parent::setUp();
        $mock                                         = m::mock('\Antares\Customfields\Model\FieldView');
        $mock->shouldReceive('query')->andReturnSelf()
                ->shouldReceive('where')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('get')->withNoArgs()->andReturnSelf()
                ->shouldReceive('select')->with(m::type('array'))->andReturnSelf();
        $this->app['antares.customfields.model.view'] = $mock;
    }

    /**
     * @see parent::teraDown
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * test contructing
     */
    public function testConstruct()
    {
        $request   = m::mock('\Illuminate\Http\Request');
        $fieldData = m::mock('\Antares\Customfields\Model\FieldData');
        $stub      = new FormHandler($request, $fieldData);
        $this->assertInstanceOf('Antares\Customfields\Events\FormHandler', $stub);
    }

    /**
     * test method onViewForm
     */
    public function testOnViewform()
    {

        $request     = m::mock('\Illuminate\Http\Request');
        $fieldData   = m::mock('\Antares\Customfields\Model\FieldData');
        $request->shouldReceive('old')->withAnyArgs()->andReturn([
            'test'
        ]);
        $stub        = new FormHandler($request, $fieldData);
        $model       = m::mock('\Antares\Model\Eloquent');
        $formBuilder = m::mock('\Antares\Contracts\Html\Form\Builder');
        $formBuilder->shouldReceive('extend')
                ->with(m::type('Closure'))
                ->andReturnSelf();
        $eventName   = 'user.profile';
        $this->assertNull($stub->onViewForm($model, $formBuilder, $eventName));
    }

}
