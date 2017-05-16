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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications\Http\Presenters\TestCase;

use Antares\Notifications\Http\Datatables\Notifications as Datatables;
use Antares\Notifications\Http\Presenters\IndexPresenter;
use Antares\Notifications\Http\Presenters\Breadcrumb;
use Antares\Notifications\Model\Notifications;
use Antares\Testing\ApplicationTestCase;
use Antares\Html\Form\FormBuilder;
use Illuminate\View\View;
use Mockery as m;
use Exception;

class IndexPresenterTest extends ApplicationTestCase
{

    /**
     * Presenter instance
     *
     * @var IndexPresenter
     */
    protected $stub;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $breadcrumb = m::mock(Breadcrumb::class);
        $breadcrumb->shouldReceive('onTable')->once()->with(null)->andReturnNull()
                ->shouldReceive('onEdit')->once()->with(m::type(Notifications::class))->andReturnNull()
                ->shouldReceive('onCreate')->once()->with(null)->andReturnNull();

        $datatables = $this->app->make(Datatables::class);
        $this->stub = new IndexPresenter($breadcrumb, $datatables);
        $this->app['view']->addNamespace('antares/notifications', realpath(base_path() . '../../../../components/notifications/resources/views'));
    }

    /**
     * Tests Antares\Notifications\Http\Presenters\IndexPresenter::edit
     * 
     * @test
     */
    public function testGetForm()
    {
        $model = new Notifications();
        $this->assertInstanceOf(FormBuilder::class, $this->stub->getForm($model));
    }

    /**
     * Tests Antares\Notifications\Http\Presenters\IndexPresenter::preview
     * 
     * @test
     */
    public function testPreview()
    {
        $this->assertInstanceOf(View::class, $this->stub->preview([]));
    }

    /**
     * Tests Antares\Notifications\Http\Presenters\IndexPresenter::create
     * 
     * @test
     */
    public function testCreate()
    {
        $model = new Notifications();
        $this->assertInstanceOf(View::class, $this->stub->create($model));
    }

    /**
     * Tests Antares\Notifications\Http\Presenters\IndexPresenter::edit
     */
    public function testEdit()
    {
        $model = new Notifications();
        $this->assertInstanceOf(View::class, $this->stub->edit($model, 'en'));
    }

    /**
     * Tests Antares\Notifications\Http\Presenters\IndexPresenter::table
     */
    public function testTable()
    {
        $this->assertInstanceOf(View::class, $this->stub->table());
    }

    /**
     * Tests Antares\Notifications\Http\Presenters\IndexPresenter::view
     */
    public function testView()
    {
        try {
            $this->stub->view('foo', [], []);
        } catch (Exception $e) {
            $this->assertSame("View [admin.index.foo] not found.", $e->getMessage());
        }
        $this->assertInstanceOf(View::class, $this->stub->view('index', []));
    }

}
