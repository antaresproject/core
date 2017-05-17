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

namespace Antares\Users\Http\Presenters\TestCase;

use Antares\Testing\ApplicationTestCase;
use Antares\Users\Http\Presenters\User;
use Mockery as m;

class UserTest extends ApplicationTestCase
{

    /**
     * Test Antares\Users\Http\Presenters\User::form() method.
     *
     * @test
     */
    public function testFormMethod()
    {
        $datatables = m::mock(\Antares\Users\Http\Datatables\Users::class);
        $breadcrumb = m::mock(\Antares\Users\Http\Breadcrumb\Breadcrumb::class);
        $breadcrumb->shouldReceive('onCreateOrEdit')->andReturnSelf();
        $stub       = new User($datatables, $breadcrumb);
        $this->assertInstanceOf(\Antares\Users\Http\Form\User::class, $stub->form(new \Antares\Model\User()));
    }

}
