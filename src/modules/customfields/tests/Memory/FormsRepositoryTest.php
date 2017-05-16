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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Antares\Customfields\Memory\FormsRepository;
use Antares\Testing\TestCase;

class FormsRepositoryTest extends TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        m::close();
    }

    /**
     * Test Antares\Customfields\Memory\FormsRepository::initiate()
     * method.
     *
     * @test
     */
    public function testInitiateMethod()
    {
        $stub = new FormsRepository('test', [], $this->app);

        $this->assertEquals([], $stub->initiate());
    }

    /**
     * Test Antares\Customfields\Memory\FormsRepository::initiate() method.
     *
     * @test
     */
    public function testRetrieveMethod()
    {
        $app = $this->app;

        $app->instance('Antares\Customfields\Model\FieldCategory', $eloquent = m::mock('FieldCategory'));

        $eloquent->shouldReceive('newInstance')->once()->andReturn($eloquent)
                ->shouldReceive('where')->once()->with('name', '=', 'user')->andReturnSelf()
                ->shouldReceive('get')->once()->andReturn([
            0 => new Fluent([
                'name'  => 'user',
                'id'    => 2,
                'value' => 'user',
                'group' => [
                    0 => new Fluent([
                        'name'  => 'profile',
                        'id'    => 2,
                        'value' => 'profile',
                            ]),
                ]
                    ]),
        ]);

        $stub = new FormsRepository('test', [], $app);
        $this->assertEquals('user.profile', $stub->retrieve('user.profile'));
    }

    /**
     * Test Antares\Customfields\Memory\FormsRepository::finish() method.
     *
     * @test
     */
    public function testFinishMethod()
    {
        $app = $this->app;

        $app->instance('Antares\Customfields\Model\FieldCategory', $eloquent   = m::mock('FieldCategory'));
        $app->instance('Antares\Customfields\Model\FieldGroup', $fieldGroup = m::mock('FieldGroup'));

        $fooEntity = m::mock('FooEntityMock');
        $fooEntity->shouldReceive('fill')->andReturnNull();
        $fooEntity->shouldReceive('save')->andReturn(true);

        $checkWithCountQuery = m::mock('\Illuminate\Database\Query\Builder');
        $checkWithCountQuery->shouldReceive('first')->andReturn($fooEntity);

        $fieldGroup->shouldReceive('where')->with('name', 'profile')->andReturn($checkWithCountQuery);
        $fooEntity->shouldReceive('group')->andReturn($fieldGroup);
        $fooEntity->id = 1;
        $eloquent->shouldReceive('newInstance')->once()->andReturn($eloquent)
                ->shouldReceive('search')->once()->with('foo')
                ->andReturn($checkWithCountQuery);
        $eloquent->id  = 1;

        $stub = new FormsRepository('test', [], $app);

        $items = [
            'foo.profile' => 'foo.profile'
        ];
        $this->assertTrue($stub->finish($items));
    }

}
