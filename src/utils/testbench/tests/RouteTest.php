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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
 namespace Antares\Testbench\TestCase;

class RouteTest extends \Antares\Testbench\TestCase
{
    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application    $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['router']->get('hello', function () {
            return 'hello world';
        });

        $app['router']->resource('foo', 'Antares\Testbench\TestCase\FooController');
    }

    /**
     * Test GET hello route.
     *
     * @test
     */
    public function testGetHelloRoute()
    {
        $crawler = $this->call('GET', 'hello');

                $this->assertEquals('hello world', $crawler->getContent());
    }

    /**
     * Test GET foo/index route using action.
     *
     * @test
     */
    public function testGetFooIndexRouteUsingAction()
    {
        $crawler = $this->action('GET', '\Antares\Testbench\TestCase\FooController@index');

        $this->assertResponseOk();
        $this->assertEquals('FooController@index', $crawler->getContent());
    }

    /**
     * Test GET foo/index route using call.
     *
     * @test
     */
    public function testGetFooIndexRouteUsingCall()
    {
        $crawler = $this->call('GET', 'foo');

        $this->assertResponseOk();
        $this->assertEquals('FooController@index', $crawler->getContent());
    }
}

class FooController extends \Illuminate\Routing\Controller
{
    public function index()
    {
        return 'FooController@index';
    }
}
