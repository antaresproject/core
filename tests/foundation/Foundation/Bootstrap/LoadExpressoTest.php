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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
 namespace Antares\Foundation\Bootstrap\TestCase;

use Illuminate\Pagination\Paginator;
use Mockery as m;
use Antares\Testing\TestCase;
use Antares\Support\Facades\Meta;

class LoadExpressoTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->make('Antares\Foundation\Bootstrap\LoadExpresso')->bootstrap($app);
    }

    /**
     * Test Blade::extend() is registered.
     *
     * @test
     */
    public function testBladeExtendIsRegistered()
    {
        $compiler = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $this->assertEquals('<?php

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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
 echo app("antares.decorator")->render("foo"); ?>', $compiler->compileString('@decorator("foo")'));
    }

    /**
     * Test HTML::title() macro.
     *
     * @test
     */
    public function testHtmlTitleMacro()
    {
        $this->app['antares.platform.memory'] = $memory = m::mock('\Antares\Contracts\Memory\Provider');

        Meta::shouldReceive('get')->once()->with('title', '')->andReturn('');

        Paginator::currentPageResolver(function () {
            return 1;
        });

        $memory->shouldReceive('get')->once()->with('site.name', '')->andReturn('Foo');

        $this->assertEquals('<title>Foo</title>', $this->app['html']->title());
    }

    /**
     * Test HTML::title() macro.
     *
     * @test
     */
    public function testHtmlTitleMacroWithPageNumber()
    {
        $this->app['antares.platform.memory'] = $memory = m::mock('\Antares\Contracts\Memory\Provider');

        Meta::shouldReceive('get')->once()->with('title', '')->andReturn('');
        Meta::shouldReceive('get')->once()
            ->with('html::title.format.site', '{site.name} (Page {page.number})')
            ->andReturn('{site.name} (Page {page.number})');

        Paginator::currentPageResolver(function () {
            return 5;
        });

        $memory->shouldReceive('get')->once()->with('site.name', '')->andReturn('Foo');

        $this->assertEquals('<title>Foo (Page 5)</title>', $this->app['html']->title());
    }

    /**
     * Test HTML::title() macro with page title.
     *
     * @test
     */
    public function testHtmlTitleMacroWithPageTitle()
    {
        $this->app['antares.platform.memory'] = $memory = m::mock('\Antares\Contracts\Memory\Provider');

        Paginator::currentPageResolver(function () {
            return 1;
        });

        Meta::shouldReceive('get')->once()->with('title', '')->andReturn('Foobar');
        Meta::shouldReceive('get')->once()
            ->with('html::title.format.page', '{page.title} &mdash; {site.name}')
            ->andReturn('{page.title} &mdash; {site.name}');

        $memory->shouldReceive('get')->once()
                ->with('site.name', '')->andReturn('Foo');

        $this->assertEquals('<title>Foobar &mdash; Foo</title>', $this->app['html']->title());
    }

    /**
     * Test HTML::title() macro with page title
     * and number.
     *
     * @test
     */
    public function testHtmlTitleMacroWithPageTitleAndNumber()
    {
        $this->app['antares.platform.memory'] = $memory = m::mock('\Antares\Contracts\Memory\Provider');

        Paginator::currentPageResolver(function () {
            return 5;
        });

        Meta::shouldReceive('get')->once()
            ->with('html::title.format.site', '{site.name} (Page {page.number})')
            ->andReturn('{site.name} (Page {page.number})');
        Meta::shouldReceive('get')->once()
            ->with('html::title.format.page', '{page.title} &mdash; {site.name}')
            ->andReturn('{page.title} &mdash; {site.name}');

        $memory->shouldReceive('get')->once()
            ->with('site.name', '')->andReturn('Foo');

        $this->assertEquals('<title>Foobar &mdash; Foo (Page 5)</title>', $this->app['html']->title('Foobar'));
    }

    /**
     * Test Antares\Decorator navbar is registered.
     *
     * @test
     */
    public function testDecoratorIsRegistered()
    {
        $stub = $this->app['antares.decorator'];
        $view = $stub->render('navbar', []);

        $this->assertInstanceOf('\Antares\View\Decorator', $stub);
        $this->assertInstanceOf('\Illuminate\View\View', $view);
        $this->assertEquals('antares/foundation::components.navbar', $view->getName());
    }
}
