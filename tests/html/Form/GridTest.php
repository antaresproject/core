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
use Antares\Html\Form\Grid;
use Mockery as m;

class GridTest extends ApplicationTestCase
{

    /**
     * Fieldset config.
     *
     * @return array
     */
    private function getFieldsetTemplates()
    {
        return [
            'select'   => [],
            'textarea' => [],
            'input'    => [],
            'password' => [],
            'file'     => [],
            'radio'    => [],
        ];
    }

    /**
     * Test instanceof Antares\Html\Form\Grid.
     *
     * @test
     */
    public function testInstanceOfGrid()
    {
        $app                                           = new Container();
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');

        $config->shouldReceive('get')->once()
                ->with('antares/html::form', [])->andReturn([
            'submit'     => 'Submit',
            'attributes' => ['id' => 'foo'],
            'view'       => 'foo',
        ]);

        $stub             = new Grid($app);
        $stub->attributes = ['class' => 'foobar'];

        $refl       = new \ReflectionObject($stub);
        $attributes = $refl->getProperty('attributes');
        $submit     = $refl->getProperty('submit');
        $view       = $refl->getProperty('view');

        $attributes->setAccessible(true);
        $submit->setAccessible(true);
        $view->setAccessible(true);

        $this->assertInstanceOf('\Antares\Html\Form\Grid', $stub);
        $this->assertEquals('Submit', $submit->getValue($stub));
        $this->assertEquals('foo', $view->getValue($stub));

        $this->assertEquals('foo', $stub->view());
        $this->assertEquals('foo', $stub->view);
        $this->assertEquals(['id' => 'foo', 'class' => 'foobar'], $attributes->getValue($stub));
    }

    /**
     * Test Antares\Html\Form\Grid::row() method.
     *
     * @test
     */
    public function testWithMethod()
    {
        $stub = new Grid($this->getContainer());
        $mock = new \Illuminate\Support\Fluent();
        $stub->with($mock);

        $refl = new \ReflectionObject($stub);
        $row  = $refl->getProperty('row');
        $row->setAccessible(true);

        $this->assertEquals($mock, $row->getValue($stub));
        $this->assertEquals($mock, $stub->row());
        $this->assertTrue(isset($stub->row));
    }

    /**
     * Test Antares\Html\Form\Grid::layout() method.
     *
     * @test
     */
    public function testLayoutMethod()
    {
        $stub = new Grid($this->getContainer());

        $refl = new \ReflectionObject($stub);
        $view = $refl->getProperty('view');
        $view->setAccessible(true);

        $stub->layout('horizontal');
        $this->assertEquals('antares/foundation::layouts.antares.partials.form.horizontal', $view->getValue($stub));

        $stub->layout('vertical');
        $this->assertEquals('antares/foundation::layouts.antares.partials.form.vertical', $view->getValue($stub));

        $stub->layout('foo');
        $this->assertEquals('foo', $view->getValue($stub));
    }

    /**
     * Test Antares\Html\Form\Grid::attributes() method.
     *
     * @test
     */
    public function testAttributesMethod()
    {
        $stub = new Grid($this->getContainer());

        $refl       = new \ReflectionObject($stub);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);

        $stub->attributes(['class' => 'foo']);

        $this->assertEquals(['class' => 'foo'], $attributes->getValue($stub));
        $this->assertEquals(['class' => 'foo'], $stub->attributes());

        $stub->attributes('id', 'foobar');

        $this->assertEquals(['id' => 'foobar', 'class' => 'foo'], $attributes->getValue($stub));
        $this->assertEquals(['id' => 'foobar', 'class' => 'foo'], $stub->attributes());
    }

    /**
     * Test Antares\Html\Form\Grid::fieldset() method.
     *
     * @test
     */
    public function testFieldsetMethod()
    {
        $app                                           = new Container();
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Antares\Contracts\Html\Form\Control']    = $control                                       = m::mock('\Antares\Html\Form\Control');
        $app['Antares\Contracts\Html\Form\Template']   = $presenter                                     = m::mock('\Antares\Html\Form\BootstrapThreePresenter');

        $config->shouldReceive('get')->twice()
                ->with('antares/html::form.templates', [])->andReturn($this->getFieldsetTemplates())
                ->shouldReceive('get')->once()
                ->with('antares/html::form', [])->andReturn([
            'templates' => $this->getFieldsetTemplates(),
            'presenter' => 'Antares\Html\Form\BootstrapThreePresenter',
        ]);
        $control->shouldReceive('setTemplates')->twice()->with($this->getFieldsetTemplates())->andReturnSelf()
                ->shouldReceive('setPresenter')->twice()->with($presenter)->andReturnSelf()
                ->shouldReceive('generate')->twice();

        $stub      = new Grid($app);
        $stub->name('foo');
        $refl      = new \ReflectionObject($stub);
        $fieldsets = $refl->getProperty('fieldsets');
        $fieldsets->setAccessible(true);

        $this->assertInstanceOf('\Antares\Support\Collection', $fieldsets->getValue($stub));
        $this->assertEquals([], $fieldsets->getValue($stub)->toArray());

        $stub->fieldset(function ($f) {
            $f->control('text', 'email');
        });
        $stub->fieldset('Foobar', function ($f) {
            $f->control('text', 'email');
        });

        $fieldset = $fieldsets->getValue($stub);

        $this->assertInstanceOf('\Antares\Html\Form\Fieldset', $fieldset[0]);

        $this->assertInstanceOf('\Antares\Html\Form\Field', $stub->find('email'));

        $this->assertInstanceOf('\Antares\Html\Form\Fieldset', $fieldset[1]);
        $this->assertEquals('Foobar', $fieldset[1]->name);
        $this->assertInstanceOf('\Antares\Html\Form\Field', $stub->find('foobar.email'));
        $this->assertEquals('email', $stub->find('foobar.email')->name);
    }

    /**
     * Test Antares\Html\Form\Grid::hidden() method.
     *
     * @test
     */
    public function testHiddenMethod()
    {
        $stub        = new Grid($app         = $this->getContainer());
        $app['form'] = $form        = m::mock('\Illuminate\Html\FormBuilder');

        $form->shouldReceive('hidden')->once()
                ->with('foo', 'foobar', m::any())->andReturn('hidden_foo')
                ->shouldReceive('hidden')->once()
                ->with('foobar', 'stubbed', m::any())->andReturn('hidden_foobar');

        $stub->with(new \Illuminate\Support\Fluent([
            'foo'    => 'foobar',
            'foobar' => 'foo',
        ]));

        $stub->hidden('foo');
        $stub->hidden('foobar', function ($f) {
            $f->value('stubbed');
        });

        $refl    = new \ReflectionObject($stub);
        $hiddens = $refl->getProperty('hiddens');
        $hiddens->setAccessible(true);

        $data = $hiddens->getValue($stub);
        $this->assertEquals('hidden_foo', $data['foo']);
        $this->assertEquals('hidden_foobar', $data['foobar']);
    }

    /**
     * Test Antares\Html\Form\Grid::find() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testFindMethodThrowsException()
    {
        $stub = new Grid($this->getContainer());

        $stub->find('foobar.email');
    }

    /**
     * Test Antares\Html\Form\Grid magic method __call() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodCallThrowsException()
    {
        $stub = new Grid($this->getContainer());

        $stub->invalidMethod();
    }

    /**
     * Test Antares\Html\Form\Grid magic method __get() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodGetThrowsException()
    {
        $stub = new Grid($this->getContainer());

        $stub->invalidProperty;
    }

    /**
     * Test Antares\Html\Form\Grid magic method __set() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodSetThrowsException()
    {
        $stub = new Grid($this->getContainer());

        $stub->invalidProperty = ['foo'];
    }

    /**
     * Test Antares\Html\Form\Grid magic method __set() throws
     * exception when $values is not an array.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
    {
        $stub = new Grid($this->getContainer());

        $stub->attributes = 'foo';
    }

    /**
     * Test Antares\Html\Form\Grid::of() method throws exception.
     *
     * @expectedException \RuntimeException
     */
    public function testOfMethodThrowsException()
    {
        $stub = new Grid($this->getContainer());

        $stub->of('foo');
    }

    /**
     * Test Antares\Html\Form\Grid magic method __isset() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodIssetThrowsException()
    {
        $stub = new Grid($this->getContainer());

        isset($stub->invalidProperty) ? true : false;
    }

    /**
     * Test Antares\Html\Form\Grid::resource() method as POST to create.
     *
     * @test
     */
    public function testResourceMethodAsPost()
    {
        $stub = new Grid($this->getContainer());

        $listener = m::mock('\Antares\Html\Form\PresenterInterface');
        $model    = m::mock('\Illuminate\Database\Eloquent\Model');


        $stub->resource($listener, 'antares::users', $model);
    }

    /**
     * Test Antares\Html\Form\Grid::resource() method as PUT to update.
     *
     * @test
     */
    public function testResourceMethodAsPut()
    {
        $stub = new Grid($this->getContainer());

        $listener      = m::mock('\Antares\Html\Form\PresenterInterface');
        $model         = m::mock('\Illuminate\Database\Eloquent\Model');
        $model->exists = true;
        $model->shouldReceive('getKey')->once()->andReturn(20);


        $stub->resource($listener, 'antares::users', $model);
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
