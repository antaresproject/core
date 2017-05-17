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

namespace Antares\Memory\Handlers\TestCase;

use Mockery as m;
use Antares\Memory\Handlers\Runtime;

class RuntimeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Stub instance.
     *
     * @var \Antares\Memory\Handlers\Runtime
     */
    private $stub = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->stub = new Runtime('stub', []);
    }

    /**
     * Test Antares\Memory\Handlers\Runtime::__construct().
     *
     * @test
     */
    public function testConstructMethod()
    {
        $refl    = new \ReflectionObject($this->stub);
        $name    = $refl->getProperty('name');
        $storage = $refl->getProperty('storage');

        $name->setAccessible(true);
        $storage->setAccessible(true);

        $this->assertEquals('runtime', $storage->getValue($this->stub));
        $this->assertEquals('stub', $name->getValue($this->stub));
    }

    /**
     * Test Antares\Memory\Handlers\Runtime::initiate().
     *
     * @test
     */
    public function testInitiateMethod()
    {
        $this->assertEquals([], $this->stub->initiate());
    }

    /**
     * Test Antares\Memory\Handlers\Runtime::finish().
     *
     * @test
     */
    public function testFinishMethod()
    {
        $this->assertTrue($this->stub->finish());
    }

}
