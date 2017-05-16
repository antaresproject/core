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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Builder\Tests;

use Antares\Tester\Builder\Generator as Stub;
use Antares\Tester\Contracts\ClassValidator;
use Antares\Contracts\Extension\Dispatcher;
use Antares\Tester\TesterServiceProvider;
use Antares\Testing\ApplicationTestCase;
use Antares\Tester\Contracts\Extractor;
use Illuminate\Contracts\View\Factory;
use Antares\Tester\Builder\Generator;
use Mockery as m;

class GeneratorTest extends ApplicationTestCase
{

    /**
     * @var Generator 
     */
    protected $stub;

    /**
     * @inherit
     */
    public function setUp()
    {
        $this->addProvider(TesterServiceProvider::class);
        parent::setUp();
    }

    /**
     * Test Antares\Tester\Builder\Generator::__construct() method.
     *
     * @test
     */
    public function testConstruct()
    {
        $stub = new Stub(m::mock(Extractor::class), m::mock(ClassValidator::class));
        $this->assertInstanceOf(Generator::class, $stub);
    }

    /**
     * Test Antares\Tester\Builder\Generator::build() method.
     *
     * @test
     */
    public function testBuild()
    {
        $extractor                      = m::mock(Extractor::class);
        $validator                      = m::mock(ClassValidator::class);
        $attributes                     = [
            'id'        => 'foo',
            'validator' => 'TestValidator',
            'title'     => 'Test Function'
        ];
        $extractor->shouldReceive('generateScripts')
                ->withAnyArgs()
                ->andReturnSelf();
        $validator->shouldReceive('isValid')->with($attributes)->andReturn(true);
        $stub                           = new Stub($extractor, $validator);
        $view                           = m::mock(Factory::class);
        $expects                        = 'result';
        $view->shouldReceive('render')->withAnyArgs()->andReturn($expects);
        $view->shouldReceive('make')->withAnyArgs()->andReturnSelf();
        $this->app[Factory::class]      = $view;
        $this->app['antares.extension'] = $extension                      = m::mock(Dispatcher::class);
        $extension->shouldReceive('isActive')->with('tester')->andReturn(true);
        $this->assertSame($stub->build('foo', $attributes), $expects);
    }

}
