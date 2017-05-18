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

namespace Antares\Html\TestCase;

use Mockery as m;
use Antares\Html\HtmlBuilder;

class HtmlBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Routing\UrlGenerator
     */
    private $url = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->url = m::mock('\Illuminate\Routing\UrlGenerator');
    }

    /**
     * Test Antares\Html\HtmlBuilder::create() with content.
     *
     * @test
     */
    public function testCreateWithContent()
    {
        $stub     = new HtmlBuilder($this->url);
        $expected = '<div class="foo">Bar</div>';
        $output   = $stub->create('div', 'Bar', ['class' => 'foo']);

        $this->assertEquals($expected, $output);
    }

    /**
     * Test Antares\Html\HtmlBuilder::create() without content.
     *
     * @test
     */
    public function testCreateWithoutContent()
    {
        $stub     = new HtmlBuilder($this->url);
        $expected = '<img src="hello.jpg" class="foo">';
        $output   = $stub->create('img', [
            'src'   => 'hello.jpg',
            'class' => 'foo',
        ]);

        $this->assertEquals($expected, $output);

        $expected = '<img src="hello.jpg" class="foo">';
        $output   = $stub->create('img', null, [
            'src'   => 'hello.jpg',
            'class' => 'foo',
        ]);

        $this->assertEquals($expected, $output);
    }

    /**
     * Test Antares\Html\HtmlBuilder::entities() method.
     *
     * @test
     */
    public function testEntitiesMethod()
    {
        $stub   = new HtmlBuilder($this->url);
        $output = $stub->raw('<img src="foo.jpg">');

        $this->assertEquals('<img src="foo.jpg">', $stub->entities($output));

        $output = '<img src="foo.jpg">';
        $this->assertEquals('&lt;img src=&quot;foo.jpg&quot;&gt;', $stub->entities($output));
    }

    /**
     * Test Antares\Html\HtmlBuilder::raw() method.
     *
     * @test
     */
    public function testRawExpressionMethod()
    {
        $stub = new HtmlBuilder($this->url);
        $this->assertInstanceOf('\Antares\Support\Expression', $stub->raw('hello'));
    }

    /**
     * Test Antares\Html\HtmlBuilder::decorate() method.
     *
     * @test
     */
    public function testDecorateMethod()
    {
        $stub = new HtmlBuilder($this->url);

        $output   = $stub->decorate(['class' => 'span4 table'], ['id' => 'foobar']);
        $expected = ['id' => 'foobar', 'class' => 'span4 table'];
        $this->assertEquals($expected, $output);

        $output   = $stub->decorate(['class' => 'span4 !span12'], ['class' => 'span12']);
        $expected = ['class' => 'span4'];
        $this->assertEquals($expected, $output);

        $output   = $stub->decorate(['id' => 'table'], ['id' => 'foobar', 'class' => 'span4']);
        $expected = ['id' => 'table', 'class' => 'span4'];
        $this->assertEquals($expected, $output);
    }

    /**
     * Test Antares\Html\HtmlBuilder methods use HtmlBuilder::raw() and
     * return Antares\Support\Expression.
     *
     * @test
     */
    public function testHtmlBuilderMethodsReturnAsExpression()
    {
        $url = $this->url;

        $url->shouldReceive('asset')->once()->with('foo.png', false)->andReturn('foo.png')
                ->shouldReceive('to')->once()->with('foo', m::type('Array'), '')->andReturn('foo');

        $stub = new HtmlBuilder($url);
        $stub->macro('foo', function () {
            return 'foo';
        });

        $stub->macro('foobar', function () {
            return new \Illuminate\Support\Fluent();
        });

        $image  = $stub->image('foo.png');
        $link   = $stub->link('foo');
        $mailto = $stub->mailto('hello@antaresplatform.com');
        $ul     = $stub->ul(['foo' => ['bar' => 'foobar']]);
        $foo    = $stub->foo();
        $foobar = $stub->foobar();

        $this->assertInstanceOf('\Antares\Support\Expression', $image);
        $this->assertInstanceOf('\Antares\Support\Expression', $link);
        $this->assertInstanceOf('\Antares\Support\Expression', $mailto);
        $this->assertInstanceOf('\Antares\Support\Expression', $ul);
        $this->assertInstanceOf('\Antares\Support\Expression', $foo);
        $this->assertInstanceOf('\Illuminate\Support\Fluent', $foobar);
    }

    /**
     * Test Antares\Html\HtmlBuilder::__call() persist since we're using
     * static for macros (this won't happen before).
     *
     * @test
     */
    public function testMagicCallMethodPersistWhenItShouldnt()
    {
        $this->assertInstanceOf('\Antares\Support\Expression', with(new HtmlBuilder($this->url))->foo());
        $this->assertInstanceOf('\Illuminate\Support\Fluent', with(new HtmlBuilder($this->url))->foobar());
    }

    /**
     * Test Antares\Html\HtmlBuilder::__call() method throws exception.
     *
     * @expectedException \BadMethodCallException
     */
    public function testMagicCallMethodThrowsException()
    {
        with(new HtmlBuilder($this->url))->missing();
    }

}
