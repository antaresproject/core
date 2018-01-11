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

namespace Antares\Testing;

//use Antares\Testbench\TestCase as TestbenchTestCase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Console\Kernel;
use Antares\Foundation\Application;
use Exception;
use Mockery;

abstract class TestCase extends BaseTestCase
{

    use CreatesApplication;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     * @throws \Exception
     */
    public function app()
    {
        $path = __DIR__ . '/../../../../../../bootstrap/app.php';


        if (!file_exists($path)) {
            throw new Exception('File [' . $path . '] does not exist.');
        }

        $app = new \Antares\Foundation\Application(realpath($this->getBasePath()));


        $app->singleton(
                \Illuminate\Contracts\Http\Kernel::class, \App\Http\Kernel::class
        );

        $app->singleton(
                \Illuminate\Contracts\Console\Kernel::class, \App\Console\Kernel::class
        );


        $app->singleton(
                \Illuminate\Contracts\Debug\ExceptionHandler::class, \Antares\Exception\Handler::class
        );


        $app->make(Kernel::class)->bootstrap();
        \Illuminate\Support\Facades\View::addLocation(getcwd() . '/resources/views/default');
        return $app;
    }

    public function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * additional providers
     *
     * @var array
     */
    protected $providers = [];

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = $this->app();

        $bootstraps = [
            'Antares\Foundation\Bootstrap\LoadFoundation',
            'Antares\Users\Bootstrap\UserAccessPolicy',
            'Antares\Extension\Bootstrap\LoadExtension',
            'Antares\View\Bootstrap\LoadCurrentTheme',
        ];
        foreach ($bootstraps as $bootstrap) {
            $app->make($bootstrap)->bootstrap($app);
        }

        return $app;
    }

    /**
     * Get application aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getApplicationAliases($app)
    {
        return $app['config']['app.aliases'];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [];
    }

    /**
     * add additional provider to providers stack
     *
     * @param String $className
     * @return array
     */
    public function addProvider($className)
    {
        if (!class_exists($className)) {
            return;
        }
        $this->providers = array_merge($this->providers, [$className]);

        return $this->providers;
    }

    /**
     * Get application providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getApplicationProviders($app)
    {
        return array_merge($app['config']['app.providers'], $this->providers);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [];
    }

    /**
     * Get base path.
     *
     * @return string
     */
    protected function getBasePath()
    {
        return __DIR__ . '/../fixture';
    }

    /**
     * Resolve application implementation.
     *
     * @return \Illuminate\Foundation\Application
     */
    protected function resolveApplication()
    {
        $app = new Application($this->getBasePath());

        $app->bind('Illuminate\Foundation\Bootstrap\LoadConfiguration', 'Antares\Config\Bootstrap\LoadConfiguration');

        return $app;
    }

    /**
     * Resolve application implementation.
     *
     * @param \Illuminate\Foundation\Application  $app
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Http\Kernel', 'Antares\Testing\Http\Kernel');
    }

}
