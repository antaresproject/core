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

namespace Antares\Notifier\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Notifier\NotifierManager;

class NotifierManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Notifier\NotifierManager::createDefaultDriver()
     * method.
     *
     * @test
     */
    public function testCreateDefaultDriverMethod()
    {
        $app = new Container();

        $app['config'] = $config        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['mailer'] = $mailer        = m::mock('\Illuminate\Contracts\Mail\Mailer');

        $config->shouldReceive('get')->once()
                ->with('antares/notifier::driver', 'laravel')->andReturn('laravel');

        $stub = new NotifierManager($app);

        $this->assertInstanceOf('\Antares\Notifier\Handlers\Laravel', $stub->driver());
    }

    /**
     * Test Antares\Notifier\NotifierManager::createAntaresDriver()
     * method.
     *
     * @test
     */
    public function testCreateAntaresDriverMethod()
    {
        $app = new Container();

        $app['antares.notifier.email'] = $app['antares.mail']           = $mailer                        = m::mock('\Antares\Notifier\Mailer');
        $app['antares.memory']         = $memory                        = m::mock('\Antares\Memory\MemoryManager');

        $memory->shouldReceive('makeOrFallback')->once()->andReturn(m::mock('\Antares\Contracts\Memory\Provider'));

        $stub = new NotifierManager($app);

        $this->assertInstanceOf('\Antares\Notifier\Handlers\Antares', $stub->driver('antares'));
    }

    /**
     * Test Antares\Notifier\NotifierManager::createLaravelDriver()
     * method.
     *
     * @test
     */
    public function testCreateLaravelDriverMethod()
    {
        $app = new Container();

        $app['mailer'] = $mailer        = m::mock('\Illuminate\Contracts\Mail\Mailer');

        $stub = new NotifierManager($app);

        $this->assertInstanceOf('\Antares\Notifier\Handlers\Laravel', $stub->driver('laravel'));
    }

    /**
     * Test Antares\Notifier\NotifierManager::setDefaultDriver()
     * method.
     *
     * @test
     */
    public function testSetDefaultDriverMethod()
    {
        $app = new Container();

        $app['config'] = $config        = m::mock('\Illuminate\Contracts\Config\Repository');

        $config->shouldReceive('set')->once()
                ->with('antares/notifier::driver', 'foo')->andReturnNull();

        $stub = new NotifierManager($app);

        $stub->setDefaultDriver('foo');
    }

}
