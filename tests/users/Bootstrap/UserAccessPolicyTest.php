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

namespace Antares\Users\Bootstrap\TestCase;

use Antares\Testing\TestCase;
use Mockery as m;

class UserAccessPolicyTest extends TestCase
{

    /**
     * Test Antares\Users\Bootstrap\UserAccessPolicy::bootstrap()
     * method.
     *
     * @test
     */
    public function testBootstrapMethod()
    {
        $this->app->make('Antares\Users\Bootstrap\UserAccessPolicy')->bootstrap($this->app);

        $this->assertEquals(['Guest'], $this->app['auth']->roles());

        $user     = m::mock('\Antares\Model\User');
        $user->shouldReceive('setAttribute')->once()->andReturnSelf();
        $user->id = 1;

        $user->shouldReceive('getRoles')->once()->andReturn([
            'Administrator',
        ]);


        $this->assertEquals(
                ['Administrator'], $this->app['events']->until('antares.auth: roles', [$user, []])
        );
    }

}
