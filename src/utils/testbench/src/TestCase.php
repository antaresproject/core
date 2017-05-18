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

namespace Antares\Testbench;

use Illuminate\Database\Eloquent\Factory;
use PHPUnit_Framework_TestCase;
use Antares\Testbench\Traits\WithFactories;
use Antares\Testbench\Traits\ApplicationTrait;
use Laravel\BrowserKitTesting\Concerns\MakesHttpRequests;
use Laravel\BrowserKitTesting\Concerns\ImpersonatesUsers;
use Laravel\BrowserKitTesting\Concerns\InteractsWithSession;
use Antares\Testbench\Contracts\TestCase as TestCaseContract;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Illuminate\Foundation\Testing\Concerns\MocksApplicationServices;

abstract class TestCase extends PHPUnit_Framework_TestCase implements TestCaseContract
{

    use ApplicationTrait,
        InteractsWithContainer,
        MakesHttpRequests,
        ImpersonatesUsers,
        InteractsWithConsole,
        InteractsWithDatabase,
        InteractsWithSession,
        MocksApplicationServices,
        WithFactories;

    /**
     * The Illuminate application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * The Eloquent factory instance.
     *
     * @var \Illuminate\Database\Eloquent\Factory
     */
    protected $factory;

    /**
     * The callbacks that should be run before the application is destroyed.
     *
     * @var array
     */
    protected $beforeApplicationDestroyedCallbacks = [];

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {

        if (!$this->app) {
            $this->refreshApplication();
        }


        if (!$this->factory) {
            $this->factory = $this->app->make(Factory::class);
        }
    }

    /**
     * Refresh the application instance.
     *
     * @return void
     */
    protected function refreshApplication()
    {
        putenv('APP_ENV=testing');
        $this->app = $this->createApplication();
    }

    /**
     * Clean up the testing environment before the next test.
     */
    public function tearDown()
    {
        if ($this->app) {
            foreach ($this->beforeApplicationDestroyedCallbacks as $callback) {
                call_user_func($callback);
            }

            $this->app->flush();

            $this->app = null;
        }
    }

    /**
     * Register a callback to be run before the application is destroyed.
     *
     * @param  callable  $callback
     *
     * @return void
     */
    protected function beforeApplicationDestroyed(callable $callback)
    {
        $this->beforeApplicationDestroyedCallbacks[] = $callback;
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        
    }

}
