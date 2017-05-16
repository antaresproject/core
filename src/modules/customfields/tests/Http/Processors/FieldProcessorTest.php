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

use Antares\Customfields\Processor\FieldProcessor;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class FieldProcessorTest extends ApplicationTestCase
{

    /**
     * @see parent::setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->app['antares.customfields.model.view'] = $fieldView                                    = m::mock(\Antares\Customfields\Model\FieldView::class);
        $fieldView->shouldReceive('query')->withNoArgs()->andReturnSelf()
                ->shouldReceive('where')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('exists')->withNoArgs()->andReturn(false);
        $this->addProvider(\Antares\Brands\BrandsServiceProvider::class);

        $this->app['antares.brand'] = $brands                     = m::mock(\Antares\Brands\Model\Brands::class);
        $brands->shouldReceive('all')->withNoArgs()->andReturn(new \Illuminate\Support\Collection());

        $this->app['customfields'] = $customfields              = m::mock(\Antares\Customfields::class);
        $customfields->shouldReceive('get')->withNoArgs()->andReturn(new \Illuminate\Support\Collection());
    }

    /**
     * test constructing
     */
    public function testConstruct()
    {
        $presenter = m::mock('\Antares\Customfields\Http\Presenters\FieldPresenter');
        $validator = m::mock('\Antares\Customfields\Http\Validators\FieldValidator');

        $stub = new FieldProcessor($presenter, $validator);
        $this->assertSame(get_class($stub), 'Antares\Customfields\Processor\FieldProcessor');
    }

    /**
     * test shows method without ajax
     */
    public function testShowWithoutAjax()
    {
        $presenter = m::mock('\Antares\Customfields\Http\Presenters\FieldPresenter');
        $presenter->shouldReceive('table')->withNoArgs()->andReturn(view());

        $validator = m::mock('\Antares\Customfields\Http\Validators\FieldValidator');

        $request = m::mock('\Illuminate\Http\Request');
        $request->shouldReceive('ajax')->withNoArgs()->andReturn(false);
        $builder = m::mock('\yajra\Datatables\Html\Builder');
        $stub    = new FieldProcessor($presenter, $validator);

        $this->assertInstanceOf(\Illuminate\Contracts\View\Factory::class, $stub->show($request, $builder));
    }

    /**
     * test show with ajax
     */
    public function testShowWithAjax()
    {
        $presenter = m::mock('\Antares\Customfields\Http\Presenters\FieldPresenter');
        $presenter->shouldReceive('table')->withNoArgs()->andReturn(new \Illuminate\Http\JsonResponse());
        $validator = m::mock('\Antares\Customfields\Http\Validators\FieldValidator');
        $request   = m::mock('\Illuminate\Http\Request');
        $request->shouldReceive('ajax')->withNoArgs()->andReturn(true);
        $builder   = m::mock('\yajra\Datatables\Html\Builder');
        $stub      = new FieldProcessor($presenter, $validator);
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $stub->show($request, $builder));
    }

}
