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

namespace Antares\Extension\TestCase;

use Mockery as m;
use Antares\Extension\RouteGenerator;

class RouteGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Extension\RouteGenerator construct proper route.
     *
     * @test
     */
    public function testConstructProperRoute()
    {
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->once()->andReturn("http://localhost/laravel")
                ->shouldReceive('secure')->once()->andReturn(false);

        $stub = new RouteGenerator("foo", $request);

        $refl   = new \ReflectionObject($stub);
        $domain = $refl->getProperty('domain');
        $prefix = $refl->getProperty('prefix');

        $domain->setAccessible(true);
        $prefix->setAccessible(true);

        $this->assertNull($domain->getValue($stub));
        $this->assertEquals('foo', $prefix->getValue($stub));

        $this->assertEquals(null, $stub->domain());
        $this->assertEquals('localhost', $stub->domain(true));
        $this->assertEquals('foo', $stub->prefix());
        $this->assertEquals('laravel/foo', $stub->prefix(true));
        $this->assertEquals('foo', (string) $stub);
        $this->assertEquals('http://localhost/laravel/foo', $stub->root());
    }

    public function isDataProvider()
    {
        return [
            ['foobar', 'foo*', true],
            ['hello', '*ello', true],
            ['helloworld', 'foo*', false],
        ];
    }

    /**
     * Test Antares\Extension\RouteGenerator::is() method without domain.
     *
     * @test
     * @dataProvider isDataProvider
     */
    public function testIsMethodWithoutDomain($path, $pattern, $expected)
    {
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->once()->andReturn("http://localhost/laravel")
                ->shouldReceive('path')->once()->andReturn("acme/$path");

        $stub = new RouteGenerator("acme", $request);

        $this->assertEquals($expected, $stub->is($pattern));
    }

    /**
     * Test Antares\Extension\RouteGenerator::path method with domain.
     *
     * @test
     * @dataProvider isDataProvider
     */
    public function testIsMethodWithDomain($path, $pattern, $expected)
    {
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->once()->andReturn("http://localhost/laravel")
                ->shouldReceive('path')->once()->andReturn($path);

        $stub = new RouteGenerator("//foobar.com", $request);

        $this->assertEquals($expected, $stub->is($pattern));
    }

    /**
     * Test Antares\Extension\RouteGenerator::path method with domain
     * and prefix.
     *
     * @test
     * @dataProvider isDataProvider
     */
    public function testIsMethodWithDomainAndPrefix($path, $pattern, $expected)
    {
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->once()->andReturn("http://localhost/laravel")
                ->shouldReceive('path')->once()->andReturn("acme/{$path}");

        $stub = new RouteGenerator("//foobar.com/acme", $request);

        $this->assertEquals($expected, $stub->is($pattern));
    }

    /**
     * Test Antares\Extension\RouteGenerator::path method without domain.
     *
     * @test
     */
    public function testPathMethodWithoutDomain()
    {
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->once()->andReturn("http://localhost/laravel")
                ->shouldReceive('path')->once()->andReturn('foo')
                ->shouldReceive('path')->once()->andReturn('foo/bar');

        $stub = new RouteGenerator("foo", $request);

        $this->assertEquals('foo', $stub->path());
        $this->assertEquals('foo/bar', $stub->path());
    }

    /**
     * Test Antares\Extension\RouteGenerator::path method with domain.
     *
     * @test
     */
    public function testPathMethodWithDomain()
    {
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->once()->andReturn("http://localhost/laravel")
                ->shouldReceive('path')->once()->andReturn('/')
                ->shouldReceive('path')->once()->andReturn('bar');

        $stub = new RouteGenerator("//foobar.com", $request);

        $this->assertEquals('/', $stub->path());
        $this->assertEquals('bar', $stub->path());
    }

    /**
     * Test Antares\Extension\RouteGenerator with domain route.
     *
     * @test
     */
    public function testRouteWithDomain()
    {
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->andReturn(null)
                ->shouldReceive('secure')->andReturn(false);

        $stub1 = new RouteGenerator("//blog.antaresplatform.com", $request);
        $stub2 = new RouteGenerator("//blog.antaresplatform.com/hello", $request);
        $stub3 = new RouteGenerator("//blog.antaresplatform.com/hello/world", $request);

        $this->assertEquals("blog.antaresplatform.com", $stub1->domain());
        $this->assertEquals("/", $stub1->prefix());
        $this->assertEquals("http://blog.antaresplatform.com", $stub1->root());
        $this->assertEquals("http://blog.antaresplatform.com/foo", $stub1->to('foo'));
        $this->assertEquals("http://blog.antaresplatform.com/foo?bar", $stub1->to('foo?bar'));
        $this->assertEquals("http://blog.antaresplatform.com/foo?bar=foobar", $stub1->to('foo?bar=foobar'));

        $this->assertEquals("blog.antaresplatform.com", $stub2->domain());
        $this->assertEquals("hello", $stub2->prefix());
        $this->assertEquals("http://blog.antaresplatform.com/hello", $stub2->root());
        $this->assertEquals("http://blog.antaresplatform.com/hello/foo", $stub2->to('foo'));
        $this->assertEquals("http://blog.antaresplatform.com/hello/foo?bar", $stub2->to('foo?bar'));
        $this->assertEquals("http://blog.antaresplatform.com/hello/foo?bar=foobar", $stub2->to('foo?bar=foobar'));

        $this->assertEquals("blog.antaresplatform.com", $stub3->domain());
        $this->assertEquals("hello/world", $stub3->prefix());
        $this->assertEquals("http://blog.antaresplatform.com/hello/world", $stub3->root());
        $this->assertEquals("http://blog.antaresplatform.com/hello/world/foo", $stub3->to('foo'));
        $this->assertEquals("http://blog.antaresplatform.com/hello/world/foo?bar", $stub3->to('foo?bar'));
        $this->assertEquals("http://blog.antaresplatform.com/hello/world/foo?bar=foobar", $stub3->to('foo?bar=foobar'));
    }

    /**
     * Test Antares\Extension\RouteGenerator with domain route when
     * domain name contain wildcard.
     *
     * @test
     */
    public function testRouteWithDomainGivenWildcard()
    {
        $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('root')->andReturn('http://localhost')
                ->shouldReceive('secure')->andReturn(false);

        $stub1 = new RouteGenerator("//blog.{{domain}}", $request);
        $stub2 = new RouteGenerator("//blog.{{domain}}/hello", $request);
        $stub3 = new RouteGenerator("//blog.{{domain}}/hello/world", $request);

        $this->assertEquals("blog.localhost", $stub1->domain());
        $this->assertEquals("/", $stub1->prefix());
        $this->assertEquals("http://blog.localhost", $stub1->root());
        $this->assertEquals("http://blog.localhost/foo", $stub1->to('foo'));
        $this->assertEquals("http://blog.localhost/foo?bar", $stub1->to('foo?bar'));
        $this->assertEquals("http://blog.localhost/foo?bar=foobar", $stub1->to('foo?bar=foobar'));

        $this->assertEquals("blog.localhost", $stub2->domain());
        $this->assertEquals("hello", $stub2->prefix());
        $this->assertEquals("http://blog.localhost/hello", $stub2->root());
        $this->assertEquals("http://blog.localhost/hello/foo", $stub2->to('foo'));
        $this->assertEquals("http://blog.localhost/hello/foo?bar", $stub2->to('foo?bar'));
        $this->assertEquals("http://blog.localhost/hello/foo?bar=foobar", $stub2->to('foo?bar=foobar'));

        $this->assertEquals("blog.localhost", $stub3->domain());
        $this->assertEquals("hello/world", $stub3->prefix());
        $this->assertEquals("http://blog.localhost/hello/world", $stub3->root());
        $this->assertEquals("http://blog.localhost/hello/world/foo", $stub3->to('foo'));
        $this->assertEquals("http://blog.localhost/hello/world/foo?bar", $stub3->to('foo?bar'));
        $this->assertEquals("http://blog.localhost/hello/world/foo?bar=foobar", $stub3->to('foo?bar=foobar'));
    }

}
