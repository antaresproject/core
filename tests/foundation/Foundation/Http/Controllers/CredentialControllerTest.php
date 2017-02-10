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


namespace Antares\Foundation\Http\Controllers\TestCase;

use Mockery as m;
use Antares\Testing\TestCase;
use Antares\Support\Facades\Meta;
use Illuminate\Support\Facades\View;
use Antares\Support\Facades\Messages;
use Antares\Support\Facades\Foundation;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class CredentialControllerTest extends TestCase
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
        $validator = m::mock('\Antares\Foundation\Validation\AuthenticateUser');

        $this->app->instance('Antares\Foundation\Validation\AuthenticateUser', $validator);

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

        $this->call('GET', 'admin/login');

        $this->assertResponseOk();
        $this->assertTrue(Meta::has('title'));
    }

    /**
     * Get processor mock.
     *
     * @return \Antares\Foundation\Processor\AuthenticateUser
     */
    protected function getProcessorMock()
    {
        $processor = m::mock('\Antares\Foundation\Processor\AuthenticateUser', [
                    m::mock('\Antares\Foundation\Validation\AuthenticateUser'),
                    m::mock('\Illuminate\Contracts\Auth\Guard'),
        ]);

        $this->app->instance('Antares\Foundation\Processor\AuthenticateUser', $processor);

        return $processor;
    }

}
