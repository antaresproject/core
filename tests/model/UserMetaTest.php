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
 namespace Antares\Model\TestCase;

use Mockery as m;
use Antares\Model\UserMeta;

class UserMetaTest extends \PHPUnit_Framework_TestCase
{
    use \Antares\Support\Traits\Testing\EloquentConnectionTrait;

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Model\UserMeta::users() method.
     *
     * @test
     */
    public function testUsersMethod()
    {
        $model = new UserMeta();

        $this->addMockConnection($model);

        $stub = $model->users();

        $this->assertInstanceOf('\Illuminate\Database\Eloquent\Relations\BelongsTo', $stub);
        $this->assertInstanceOf('\Antares\Model\User', $stub->getQuery()->getModel());
    }

    /**
     * Test Antares\Model\UserMeta::search() method.
     *
     * @test
     */
    public function testScopeSearchMethod()
    {
        $query = m::mock('\Illuminate\Database\Eloquent\Builder');

        $query->shouldReceive('where')->once()->with('user_id', '=', 1)->andReturn($query)
            ->shouldReceive('where')->once()->with('name', '=', 'foo')->andReturn($query);

        with(new UserMeta())->scopeSearch($query, 'foo', 1);
    }
}
