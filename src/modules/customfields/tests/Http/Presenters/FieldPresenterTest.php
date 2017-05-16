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

use Antares\Customfields\Http\Presenters\FieldPresenter;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class FieldPresenterTest extends ApplicationTestCase
{

    /**
     * test constructing
     */
    public function testConstruct()
    {

        $mock       = m::mock('\Antares\Customfields\Http\Forms\FieldFormFactory');
        $breadcrumb = $this->app->make(\Antares\Customfields\Http\Breadcrumb\Breadcrumb::class);
        $datatable  = $this->app->make(\Antares\Customfields\Http\Datatables\Customfields::class);
        $stub       = new FieldPresenter($mock, $breadcrumb, $datatable);
        $this->assertSame(get_class($stub), 'Antares\Customfields\Http\Presenters\FieldPresenter');
    }

    /**
     * testing table method
     */
    public function testTable()
    {
        $mock       = m::mock('\Antares\Customfields\Http\Forms\FieldFormFactory');
        $builder    = $this->app->make(\Antares\Customfields\Http\Forms\FieldFormFactory::class);
        $breadcrumb = $this->app->make(\Antares\Customfields\Http\Breadcrumb\Breadcrumb::class);
        $datatable  = $this->app->make(\Antares\Customfields\Http\Datatables\Customfields::class);
        $fieldView  = m::mock('\Antares\Customfields\Model\FieldView');
        $fieldView->shouldReceive('query')->withNoArgs()->andReturnSelf()
                ->shouldReceive('where')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('get')->withAnyArgs()->andReturn(new \Illuminate\Support\Collection)
                ->shouldReceive('isEmpty')->withAnyArgs()->andReturn(true);

        $this->app['antares.customfields.model.view'] = $fieldView;

        $this->app['view']->addNamespace('antares/customfields', realpath(base_path() . '../../../../components/customfields/resources/views'));
        $stub = new FieldPresenter($mock, $breadcrumb, $datatable);
        $this->assertInstanceOf(\Illuminate\View\View::class, $stub->table($builder));
    }

    /**
     * testing form method
     * 
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testForm()
    {
        $mock       = m::mock('\Antares\Customfields\Http\Forms\FieldFormFactory');
        $mock->shouldReceive('of')
                ->with(m::type('String'), m::type('Closure'))
                ->andReturn(true)
                ->shouldReceive('build')
                ->andReturn(true);
        $breadcrumb = m::mock(\Antares\Customfields\Http\Breadcrumb\Breadcrumb::class);
        $breadcrumb->shouldReceive('onCustomFieldCreateOrEdit')->with(m::type('Object'))->andReturnSelf();


        $this->app['antares.customfields.model.category'] = $fieldCategory                                    = m::mock(\Antares\Customfields\Model\FieldCategory::class);
        $fieldCategory->shouldReceive('query')->andReturnSelf();
        $fieldCategory->shouldReceive('findOrFail')->andThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class, 302);

        $datatable = $this->app->make(\Antares\Customfields\Http\Datatables\Customfields::class);
        $stub      = new FieldPresenter($mock, $breadcrumb, $datatable);
        $eloquent  = m::mock('\Antares\Model\Eloquent');
        $eloquent->shouldReceive('getFlattenValidators')
                ->once()
                ->andReturn(array())
                ->shouldReceive('getAttribute')
                ->once()
                ->with(m::type('String'))
                ->andReturn(array());
        $route     = m::mock('\Illuminate\Routing\Route');
        $route->shouldReceive('parameter')->withAnyArgs()->andReturn(true);
        $this->assertTrue($stub->form($eloquent, 'fooAction', $route));
    }

}
