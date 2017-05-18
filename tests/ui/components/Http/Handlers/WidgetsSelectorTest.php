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
 * @package    Widgets
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Widgets\Http\Handlers\Tests;

use Antares\Widgets\Http\Handlers\WidgetsSelector;
use Antares\Testing\ApplicationTestCase;

class WidgetsSelectorTest extends ApplicationTestCase
{

    /**
     * Test Antares\Widgets\Http\Handlers\WidgetsTopMenuHandler::handle() method with authorized user.
     *
     * @test
     */
    public function testHandle()
    {
        $stub = new WidgetsSelector($this->app);
        $this->assertNull($stub->handle());
    }

}
