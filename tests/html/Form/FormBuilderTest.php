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

namespace Antares\Html\Form\TestCase;

use Antares\Testing\ApplicationTestCase;
use Illuminate\Container\Container;
use Antares\Html\Form\FormBuilder;
use Antares\Html\Form\Grid;
use Mockery as m;

class FormBuilderTest extends ApplicationTestCase
{

    /**
     * Test construct a new Antares\Html\Form\FormBuilder.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $grid = new Grid($this->getContainer());
        $stub = new FormBuilder($grid);

        $refl = new \ReflectionObject($stub);
        $name = $refl->getProperty('name');
        $grid = $refl->getProperty('grid');

        $name->setAccessible(true);
        $grid->setAccessible(true);

        $this->assertInstanceOf('\Antares\Html\Form\FormBuilder', $stub);
        $this->assertInstanceOf('\Antares\Html\Builder', $stub);
        $this->assertInstanceOf('\Illuminate\Contracts\Support\Renderable', $stub);

        $this->assertNull($name->getValue($stub));
        $this->assertNull($stub->name);
        $this->assertInstanceOf('\Antares\Html\Form\Grid', $grid->getValue($stub));
        $this->assertInstanceOf('\Antares\Html\Form\Grid', $stub->grid);
    }

    /**
     * test Antares\Html\Form\FormBuilder::__get() throws an exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodThrowsException()
    {
        $grid = new Grid($this->app);
        $stub = new FormBuilder($grid);
        $stub->someInvalidRequest;
    }

    /**
     * test Antares\Html\Form\FormBuilder::render() method.
     *
     * @test
     */
    public function testRenderMethod()
    {
        $grid = new Grid($this->app);
        $grid->layout('form');



        $data = new \Illuminate\Support\Fluent([
            'id'   => 1,
            'name' => 'Laravel',
        ]);

        $stub1 = new FormBuilder($grid);
        $stub1->extend(function ($form) use ($data) {
            $form->with($data);
            $form->attributes([
                'method' => 'POST',
                'url'    => 'http://localhost',
                'class'  => 'foo',
            ]);
        });

        $stub2 = new FormBuilder($grid);
        $stub2->extend(function ($form) use ($data) {
            $form->with($data);
            $form->attributes = [
                'method' => 'POST',
                'url'    => 'http://localhost',
                'class'  => 'foo',
            ];
        });
        ob_start();
        echo $stub1;
        $output = ob_get_contents();
        ob_end_clean();


        $this->assertEquals('mocked', $output);
        $this->assertEquals('mocked', $stub2->render());
    }

    /**
     * Get app container.
     *
     * @return Container
     */
    protected function getContainer()
    {
        $app                                           = new Container();
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');

        $config->shouldReceive('get')->once()
                ->with('antares/html::form', [])->andReturn([]);

        return $app;
    }

}
