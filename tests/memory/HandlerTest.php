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
 namespace Antares\Memory\TestCase;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInformations()
    {
        $stub = new StubHandler('stub-handler', []);

        $this->assertEquals('stub-handler', $stub->getName());
        $this->assertEquals('stub', $stub->getStorageName());
    }
}

class StubHandler extends \Antares\Memory\Handler
{
    protected $storage = 'stub';
}
