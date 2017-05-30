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

use Antares\Contracts\Html\Form\Template;
use Antares\Testing\ApplicationTestCase;
use Antares\Contracts\Html\Form\Field;
use Illuminate\Container\Container;
use Antares\Html\Form\Fieldset;
use Illuminate\Support\Fluent;
use Antares\Html\Form\Control;
use Mockery as m;

class FieldsetTest extends ApplicationTestCase
{

    /**
     * Fieldset config.
     *
     * @return array
     */
    private function getFieldsetTemplates()
    {
        return [
            'button'   => [],
            'checkbox' => [],
            'input'    => [],
            'file'     => [],
            'password' => [],
            'radio'    => [],
            'select'   => [],
            'textarea' => [],
        ];
    }

    /**
     * Get template config.
     *
     * @return array
     */
    private function getPresenterInstance()
    {
        return new StubTemplatePresenter();
    }

    /**
     * Test instance of Antares\Html\Form\Fieldset.
     *
     * @test
     */
    public function testInstanceOfFieldset()
    {
        $app                                           = $this->app;
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Antares\Contracts\Html\Form\Control']    = $control                                       = m::mock('\Antares\Html\Form\Control');
        $app['Antares\Contracts\Html\Form\Template']   = $presenter                                     = $this->getPresenterInstance();

        $config->shouldReceive('get')->once()
                ->with('antares/html::form.templates', [])->andReturn($this->getFieldsetTemplates());
        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
                ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, 'foo', function ($f) {
            $f->legend('foo');
            $f->attributes(['class' => 'foo']);
        });
        $this->assertEquals('foo', $stub->legend());

        $this->assertInstanceOf('\Antares\Html\Form\Fieldset', $stub);
        $this->assertEquals([], $stub->controls);
        $this->assertTrue(isset($stub->name));

        $this->assertEquals(['class' => 'foo'], $stub->attributes);
        $this->assertEquals('foo', $stub->name());

        $stub->attributes = ['class' => 'foobar'];
        $this->assertEquals(['class' => 'foobar'], $stub->attributes);
    }

    /**
     * Test Antares\Html\Form\Fieldset::of() method.
     *
     * @test
     */
    public function testOfMethod()
    {
        $app                                           = $this->app;
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Antares\Contracts\Html\Form\Control']    = $control                                       = m::mock('\Antares\Html\Form\Control');
        $app['Antares\Contracts\Html\Form\Template']   = $presenter                                     = $this->getPresenterInstance();

        $config->shouldReceive('get')->once()
                ->with('antares/html::form.templates', [])->andReturn($this->getFieldsetTemplates());
        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
                ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf()
                ->shouldReceive('generate')->once()->with('text');

        $stub = new Fieldset($app, function ($f) {
            $f->control('text', 'id', function ($c) {
                $c->value('Foobar');
            });
        });

        $output = $stub->of('id');

        $this->assertEquals('Foobar', $output->value);
        $this->assertEquals('Id', $output->label);
        $this->assertEquals('id', $output->id);
    }

    /**
     * Test Antares\Html\Form\Fieldset::control() method.
     *
     * @test
     */
    public function testControlMethod()
    {
        $app                                           = $this->app;
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Antares\Contracts\Html\Form\Template']   = $presenter                                     = $this->getPresenterInstance();
        $app['html']                                   = $html                                          = m::mock('\Antares\Html\HtmlBuilder');
        $app['request']                                = $request                                       = m::mock('\Illuminate\Http\Request');

        $app['Antares\Contracts\Html\Form\Control'] = $control                                    = new Control($app, $html, $request);

        $config->shouldReceive('get')->once()
                ->with('antares/html::form.templates', [])->andReturn($this->getFieldsetTemplates());
        $request->shouldReceive('old')->times(11)->andReturn([]);
        $html->shouldReceive('decorate')->times(11)->andReturn('foo');

        $stub = new Fieldset($app, function ($f) {
            $f->control('button', 'button_foo', function ($c) {
                $c->label('Foo')->value(function () {
                    return 'foobar';
                });
            });

            $f->control('checkbox', 'checkbox_foo', function ($c) {
                $c->label('Foo')->value('foobar')->checked(true);
            });

            $f->control('file', 'file_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('input:email', 'email_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('input:textarea', 'textarea_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('password', 'password_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('radio', 'radio_foo', function ($c) {
                $c->label('Foo')->value('foobar')->checked(true);
            });

            $f->control('select', 'select_foo', function ($c) {
                $c->label('Foo')->value('foobar')->options(function () {
                    return [
                        'yes' => 'Yes',
                        'no'  => 'No',
                    ];
                });
            });

            $f->control('text', 'text_foo', function ($c) {
                $c->label('Foo')->value('foobar');
            });

            $f->control('text', 'a', 'A');
        });

        $output = $stub->of('button_foo');

        $this->assertEquals('button_foo', $output->id);
        $this->assertEquals('button_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('checkbox_foo');

        $this->assertEquals('checkbox_foo', $output->id);
        $this->assertEquals('checkbox_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('email_foo');

        $this->assertEquals('email_foo', $output->id);
        $this->assertEquals('email_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('file_foo');

        $this->assertEquals('file_foo', $output->id);
        $this->assertEquals('file_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('password_foo');

        $this->assertEquals('password_foo', $output->id);
        $this->assertEquals('password_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('radio_foo');

        $this->assertEquals('radio_foo', $output->id);
        $this->assertEquals('radio_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('select_foo');

        $this->assertEquals('select_foo', $output->id);
        $this->assertEquals('select_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('text_foo');

        $this->assertEquals('text_foo', $output->id);
        $this->assertEquals('text_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('textarea_foo');

        $this->assertEquals('textarea_foo', $output->id);
        $this->assertEquals('textarea_foo', call_user_func($output->field, new Fluent(), $output));

        $output = $stub->of('a');

        $this->assertEquals('a', $output->id);
        $this->assertEquals('a', call_user_func($output->field, new Fluent(), $output));

        $controls = $stub->controls;
        $output   = end($controls);

        $this->assertEquals('a', call_user_func($output->field, new Fluent(), $output));
    }

    /**
     * Test Antares\Html\Form\Fieldset::of() method throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testOfMethodThrowsException()
    {
        $app                                           = $this->app;
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Antares\Contracts\Html\Form\Control']    = $control                                       = m::mock('\Antares\Html\Form\Control');
        $app['Antares\Contracts\Html\Form\Template']   = $presenter                                     = $this->getPresenterInstance();

        $config->shouldReceive('get')->once()
                ->with('antares/html::form.templates', [])->andReturn($this->getFieldsetTemplates());
        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
                ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, function ($f) {
            
        });

        $stub->of('id');
    }

    /**
     * Test Antares\Support\Form\Grid::attributes() method.
     *
     * @test
     */
    public function testAttributesMethod()
    {
        $app                                           = $this->app;
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Antares\Contracts\Html\Form\Control']    = $control                                       = m::mock('\Antares\Html\Form\Control');
        $app['Antares\Contracts\Html\Form\Template']   = $presenter                                     = $this->getPresenterInstance();

        $config->shouldReceive('get')->once()
                ->with('antares/html::form.templates', [])->andReturn($this->getFieldsetTemplates());
        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
                ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, function () {
            
        });

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
     * Test Antares\Html\Form\Fieldset magic method __call() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodCallThrowsException()
    {
        $app                                           = $this->app;
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Antares\Contracts\Html\Form\Control']    = $control                                       = m::mock('\Antares\Html\Form\Control');
        $app['Antares\Contracts\Html\Form\Template']   = $presenter                                     = $this->getPresenterInstance();

        $config->shouldReceive('get')->once()
                ->with('antares/html::form.templates', [])->andReturn($this->getFieldsetTemplates());
        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
                ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, function ($f) {
            
        });

        $stub->invalidMethod();
    }

    /**
     * Test Antares\Html\Form\Fieldset magic method __get() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodGetThrowsException()
    {
        $app                                           = $this->app;
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Antares\Contracts\Html\Form\Control']    = $control                                       = m::mock('\Antares\Html\Form\Control');
        $app['Antares\Contracts\Html\Form\Template']   = $presenter                                     = $this->getPresenterInstance();

        $config->shouldReceive('get')->once()
                ->with('antares/html::form.templates', [])->andReturn($this->getFieldsetTemplates());
        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
                ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, function ($f) {
            
        });

        $stub->invalidProperty;
    }

    /**
     * Test Antares\Html\Form\Fieldset magic method __set() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodSetThrowsException()
    {
        $app                                           = $this->app;
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Antares\Contracts\Html\Form\Control']    = $control                                       = m::mock('\Antares\Html\Form\Control');
        $app['Antares\Contracts\Html\Form\Template']   = $presenter                                     = $this->getPresenterInstance();

        $config->shouldReceive('get')->once()
                ->with('antares/html::form.templates', [])->andReturn($this->getFieldsetTemplates());
        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
                ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, function ($f) {
            
        });

        $stub->invalidProperty = ['foo'];
    }

    /**
     * Test Antares\Html\Form\Fieldset magic method __set() throws
     * exception when $values is not an array.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
    {
        $app                                           = $this->app;
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Antares\Contracts\Html\Form\Control']    = $control                                       = m::mock('\Antares\Html\Form\Control');
        $app['Antares\Contracts\Html\Form\Template']   = $presenter                                     = $this->getPresenterInstance();

        $config->shouldReceive('get')->once()
                ->with('antares/html::form.templates', [])->andReturn($this->getFieldsetTemplates());
        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
                ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, function ($f) {
            
        });

        $stub->attributes = 'foo';
    }

    /**
     * Test Antares\Html\Form\Fieldset magic method __isset() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodIssetThrowsException()
    {
        $app                                           = $this->app;
        $app['Illuminate\Contracts\Config\Repository'] = $config                                        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['Antares\Contracts\Html\Form\Control']    = $control                                       = m::mock('\Antares\Html\Form\Control');
        $app['Antares\Contracts\Html\Form\Template']   = $presenter                                     = $this->getPresenterInstance();

        $config->shouldReceive('get')->once()
                ->with('antares/html::form.templates', [])->andReturn($this->getFieldsetTemplates());
        $control->shouldReceive('setTemplates')->once()->with($this->getFieldsetTemplates())->andReturnSelf()
                ->shouldReceive('setPresenter')->once()->with($presenter)->andReturnSelf();

        $stub = new Fieldset($app, function ($f) {
            
        });

        isset($stub->invalidProperty) ? true : false;
    }

}

class StubTemplatePresenter implements Template
{

    /**
     * Button template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function button(Field $field)
    {
        return $field->name;
    }

    /**
     * Checkbox template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function checkbox(Field $field)
    {
        return $field->name;
    }

    /**
     * Checkboxes template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function checkboxes(Field $field)
    {
        return $field->name;
    }

    /**
     * File template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function file(Field $field)
    {
        return $field->name;
    }

    /**
     * Input template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function input(Field $field)
    {
        return $field->name;
    }

    /**
     * Password template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function password(Field $field)
    {
        return $field->name;
    }

    /**
     * Radio template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function radio(Field $field)
    {
        return $field->name;
    }

    /**
     * Select template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function select(Field $field)
    {
        return $field->name;
    }

    /**
     * Textarea template.
     *
     * @param  \Antares\Contracts\Html\Form\Field $field
     *
     * @return string
     */
    public function textarea(Field $field)
    {
        return $field->name;
    }

}
