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

use Illuminate\Container\Container;
use Antares\Notifier\NotifierServiceProvider;
use Antares\Testing\ApplicationTestCase;

class NotifierServiceProviderTest extends ApplicationTestCase
{

    /**
     * Test Antares\Notifier\NotifierServiceProvider::register() method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $stub = new NotifierServiceProvider($this->app);
        $this->assertNull($stub->register());
    }

    /**
     * Test Antares\Notifier\NotifierServiceProvider::boot() method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $stub = new NotifierServiceProvider($this->app);
        $this->assertNull($stub->boot());
    }

    /**
     * Test Antares\Notifier\NotifierServiceProvider::provides() method.
     *
     * @test
     */
    public function testProvidesMethod()
    {
        $app  = new Container();
        $stub = new NotifierServiceProvider($app);
        $this->assertEquals(['antares.notifier.sms', 'antares.notifier.email', 'antares.notifier'], $stub->provides());
    }

    /**
     * Test Antares\Notifier\NotifierServiceProvider is deferred.
     *
     * @test
     */
    public function testServiceIsDeferred()
    {
        $app  = new Container();
        $stub = new NotifierServiceProvider($app);

        $this->assertTrue($stub->isDeferred());
    }

}
