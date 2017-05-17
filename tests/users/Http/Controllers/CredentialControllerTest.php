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

namespace Antares\Users\Http\Controllers\TestCase;

use Antares\Testing\ApplicationTestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\View;
use Mockery as m;

class CredentialControllerTest extends ApplicationTestCase
{

    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $this->disableMiddlewareForAllTests();
    }

    /**
     * Bind dependencies.
     *
     * @return array
     */
    protected function bindValidation()
    {
        $validator = m::mock('\Antares\Users\Validation\AuthenticateUser');

        $this->app->instance('Antares\Users\Validation\AuthenticateUser', $validator);

        return $validator;
    }

    /**
     * Test GET /admin/login.
     *
     * @test
     */
    public function testGetLoginAction()
    {
        View::shouldReceive('make')->once()
                ->with('antares/foundation::credential.login', [], [])->andReturn('foo');

        $this->call('GET', 'antares/login');

        $this->assertResponseOk();
    }

    /**
     * Get processor mock.
     *
     * @return \Antares\Users\Processor\AuthenticateUser
     */
    protected function getProcessorMock()
    {
        $processor = m::mock('\Antares\Users\Processor\AuthenticateUser', [
                    m::mock('\Antares\Users\Validation\AuthenticateUser'),
                    m::mock('\Illuminate\Contracts\Auth\Guard'),
        ]);

        $this->app->instance('Antares\Users\Processor\AuthenticateUser', $processor);

        return $processor;
    }

}
