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

namespace Antares\Model\Memory\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Illuminate\Container\Container;
use Antares\Model\Memory\UserMetaRepository;

class UserMetaRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Container\Container
     */
    private $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();
    }

    /**
     * Test Antares\Model\Memory\UserMetaRepository::initiate()
     * method.
     *
     * @test
     */
    public function testInitiateMethod()
    {
        $stub = new UserMetaRepository('meta', [], $this->app);

        $this->assertEquals([], $stub->initiate());
    }

    /**
     * Test Antares\Model\Memory\UserMetaRepository::initiate() method.
     *
     * @test
     */
    public function testRetrieveMethod()
    {
        $app = $this->app;

        $app->instance('Antares\Model\UserMeta', $eloquent = m::mock('UserMeta'));
        $eloquent->shouldReceive('newInstance')->once()->andReturn($eloquent)
                ->shouldReceive('where')->once()->with('user_id', '=', 1)->andReturnSelf()
                ->shouldReceive('get')->once()->andReturn([
            0 => new Fluent([
                'name'  => 'foo',
                'id'    => 2,
                'value' => 'foobar',
                    ]),
        ]);

        $stub = new UserMetaRepository('meta', [], $app);


        $this->assertEquals('foobar', $stub->retrieve('foo/user-1'));
        $this->assertNull($stub->retrieve('foobar/user-1'));
    }

    /**
     * Test Antares\Model\Memory\UserMetaRepository::finish() method.
     *
     * @test
     */
    public function testFinishMethod()
    {
        $app = $this->app;

        $value = m::mock('stdClass', [
                    'id'    => 2,
                    'value' => 's:6:"foobar";',
        ]);

        $items = [
            'foo/user-1'    => 's:0:"";',
            'foobar/user-1' => 's:3:"foo";',
            'foo/user-2'    => ':to-be-deleted:',
            'foo/user-'     => 's:0:"";',
        ];

        $app->instance('Antares\Model\UserMeta', $eloquent = m::mock('UserMeta'));

        $eloquent->shouldReceive('newInstance')->times(4)->andReturn($eloquent)
                ->shouldReceive('search')->once()->with('foo', 1)
                ->andReturn($fooQuery    = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial())
                ->shouldReceive('search')->once()->with('foobar', 1)
                ->andReturn($foobarQuery = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial())
                ->shouldReceive('search')->once()->with('foo', 2)
                ->andReturn($foobarQuery)
                ->shouldReceive('save')->once()->andReturnNull();

        $fooQuery->shouldReceive('first')->andReturn($value);
        $foobarQuery->shouldReceive('first')->andReturnNull();

        $value->shouldReceive('save')->once()->andReturnNull();

        $stub = new UserMetaRepository('meta', [], $app);

        $this->assertTrue($stub->finish($items));
    }

}
