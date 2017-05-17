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

use Mockery as m;
use Illuminate\Support\Fluent;
use Antares\Html\Form\Control;

class ControlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Html\Form\Control configuration methods.
     *
     * @test
     */
    public function testTemplateMethods()
    {
        $template = ['foo' => 'foobar'];

        $app     = m::mock('\Illuminate\Contracts\Container\Container');
        $html    = m::mock('\Antares\Html\HtmlBuilder');
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Control($app, $html, $request);

        $stub->setTemplates($template);

        $this->assertEquals($template, $stub->getTemplates());
    }

    /**
     * Test Antares\Html\Form\Control::buildFluentData() method.
     *
     * @test
     */
    public function testBuildFluentDataMethod()
    {
        $app     = m::mock('\Illuminate\Contracts\Container\Container');
        $html    = m::mock('\Antares\Html\HtmlBuilder');
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('old')->once()->with('foobar')->andReturn(null);

        $row = new Fluent([
            'foobar' => function () {
                return 'Mr Derp';
            },
        ]);

        $control = new Fluent([
            'name' => 'foobar',
        ]);

        $stub = new Control($app, $html, $request);
        $stub->buildFluentData('text', $row, $control);
    }

    /**
     * Test Antares\Html\Form\Control::render() throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testRenderMethodThrowsException()
    {
        $app     = m::mock('\Illuminate\Contracts\Container\Container');
        $html    = m::mock('\Antares\Html\HtmlBuilder');
        $request = m::mock('\Illuminate\Http\Request');

        $stub = new Control($app, $html, $request);

        $stub->render(
                [], new \Illuminate\Support\Fluent(['method' => 'foo'])
        );
    }

}
