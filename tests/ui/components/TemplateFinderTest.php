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
 * @package    Widgets
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Widgets\Tests;

use Antares\UI\UIComponents\UiComponentsServiceProvider;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\UI\UIComponents\TemplateFinder as Stub;
use Illuminate\Contracts\Config\Repository;
use Antares\UI\UIComponents\TemplateFinder;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class TemplateFinderTest extends ApplicationTestCase
{

    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->addProvider(UiComponentsServiceProvider::class);
        parent::setUp();
        $this->app['config'] = $config              = m::mock(Repository::class);
        $config->shouldReceive('get')->with("antares/ui-components::templates", [])->once()->andReturn([
            'public_path'      => 'widgets/templates',
            'preview_pattern'  => 'screenshot.png',
            'preview_default'  => 'img/screenshot.png',
            'manifest_pattern' => 'template.json',
            'indexes_path'     => 'resources/views/templates'
        ]);
    }

    /**
     * Test constructing
     * 
     * @test
     */
    public function testConstruct()
    {

        $stub = new Stub($this->app);
        $this->assertInstanceOf(TemplateFinder::class, $stub);
    }

    /**
     * Test detect method
     * 
     * @test
     */
    public function testDetect()
    {
        $stub     = new Stub($this->app);
        $detected = $stub->detect();
        $all      = $detected->all();
        $this->assertNotEmpty($all);
    }

}
