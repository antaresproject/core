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

namespace Antares\Widgets\Adapter\Tests;

use Antares\UI\UIComponents\Adapter\AfterValidateAdapter as Stub;
use Antares\Testing\TestCase;

class AfterValidateAdapterTest extends TestCase
{

    /**
     * test Antares\UI\UIComponents\Adapter\AfterValidateAdapter::afterValidate
     * 
     * @test
     */
    public function testAfterValidate()
    {
        $stub = new Stub();
        $this->assertTrue(strpos($stub->afterValidate('foo'), 'js:function') !== false);
    }

}
