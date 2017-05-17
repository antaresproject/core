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

namespace Antares\Brands\TestCase;

use Antares\Brands\Composers\BrandPlaceHolder;
use Antares\Testing\ApplicationTestCase;
use Antares\UI\WidgetManager;
use Antares\Support\Fluent;
use Mockery as m;

class BrandSelectorPlaceHolderTest extends ApplicationTestCase
{

    /**
     * test contructor
     */
    public function testConstruct()
    {
        $brandsSelectorHandlerMock = m::mock('\Antares\Brands\Http\Handlers\BrandsSelectorHandler');
        $stub                      = new BrandPlaceHolder($this->app, $brandsSelectorHandlerMock);
        $this->assertEquals(get_class($stub), 'Antares\Brands\Composers\BrandPlaceHolder');
    }

    /**
     * test on booting placeholder
     */
    public function testOnBootExtension()
    {
        $this->app['antares.widget']                    = new WidgetManager($this->app);
        $viewFactory                                    = m::mock('Illuminate\Contracts\View\Factory');
        $viewFactory->shouldReceive('make')->with(m::type('string'), m::type('array'), m::type('array'))->andReturnSelf();
        $this->app['Illuminate\Contracts\View\Factory'] = $viewFactory;
        $brandsSelectorHandlerMock                      = m::mock('\Antares\Brands\Http\Handlers\BrandsSelectorHandler');
        $brandsSelectorHandlerMock->shouldReceive('handle')->andReturnSelf();
        $stub                                           = new BrandPlaceHolder($this->app, $brandsSelectorHandlerMock);
        $this->assertInstanceOf(Fluent::class, $stub->onBootExtension());
    }

}
