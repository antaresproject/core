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
 namespace Antares\Foundation\Bootstrap\TestCase;

use Antares\Testing\TestCase;

class LoadUserMetaDataTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->make('Antares\Foundation\Bootstrap\LoadUserMetaData')->bootstrap($app);
    }

    /**
     * Test instance of `antares.memory`.
     *
     * @test
     */
    public function testInstanceOfAntaresMemory()
    {
        $stub = $this->app->make('antares.memory')->driver('user');

        $this->assertInstanceOf('\Antares\Model\Memory\UserMetaProvider', $stub);
        $this->assertInstanceOf('\Antares\Memory\Provider', $stub);
    }
}
