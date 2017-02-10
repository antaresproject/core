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


namespace Antares\Foundation\Http\Composers\TestCase;

use Antares\Foundation\Http\Composers\LeftPane;
use Antares\Testing\TestCase;

class LeftPaneTest extends TestCase
{

    /**
     * test constructing
     * @test
     */
    public function testConstructing()
    {
        $this->assertInstanceOf('\Antares\Foundation\Http\Composers\LeftPane', new LeftPane());
    }

    /**
     * @test
     * test compose method
     */
    public function testCompose()
    {
        $stub = new LeftPane();
        $this->assertNull($stub->compose('pane.foo'));
    }

}
