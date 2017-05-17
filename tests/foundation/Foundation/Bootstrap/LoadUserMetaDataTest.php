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

namespace Antares\Foundation\Bootstrap\TestCase;

use Antares\Users\Bootstrap\LoadUserMetaData;
use Antares\Testing\ApplicationTestCase;

class LoadUserMetaDataTest extends ApplicationTestCase
{

    /**
     * Test instance of `antares.memory`.
     *
     * @test
     */
    public function testInstanceOfAntaresMemory()
    {
        $this->app->make(LoadUserMetaData::class)->bootstrap($this->app);
        $stub = $this->app->make('antares.memory')->driver('user');
        $this->assertInstanceOf('\Antares\Model\Memory\UserMetaProvider', $stub);
        $this->assertInstanceOf('\Antares\Memory\Provider', $stub);
    }

}
