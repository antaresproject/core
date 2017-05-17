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

namespace Antares\Model\TestCase;

use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Antares\Testing\ApplicationTestCase;
use Antares\Model\UserMeta;
use Mockery as m;

class UserMetaTest extends ApplicationTestCase
{

    use EloquentConnectionTrait;

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
