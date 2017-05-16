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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Adapter\Tests;

use Antares\Tester\Adapter\MessageAdapter as Stub;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class MessageAdapterTest extends ApplicationTestCase
{

    /**
     * @inherits
     */
    public function setUp()
    {
        parent::setUp();
        $repository          = m::mock('\Illuminate\Contracts\Config\Repository');
        $config              = require(__DIR__ . '/../fixtures/codes.php');
        $errors              = $config['errors'];
        $repository->shouldReceive('get')->with('antares/tester::codes.errors', [])->andReturn($errors)
                ->shouldReceive('get')->with("tester.codes.errors", [])->andReturn([]);
        $this->app['config'] = $repository;
    }

    /**
     * Test Antares\Tester\Adapter\MessageAdapter::__construct() method.
     *
     * @test
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('Antares\Tester\Adapter\MessageAdapter', new Stub);
    }

    /**
     * Test Antares\Tester\Adapter\MessageAdapter::getDescription() method.
     *
     * @test
     */
    public function testGetDescription()
    {
        $stub = new Stub;
        $stub->setDomain('default');
        $this->assertSame($stub->getDescription('OK'), 'All fine. Proceed as usual.');
    }

    /**
     * Test Antares\Tester\Adapter\MessageAdapter::getCode() method.
     *
     * @test
     */
    public function testGetCode()
    {
        $stub = new Stub;
        $stub->setDomain('default');
        $this->assertEquals($stub->getCode('OK'), 0);
    }

    /**
     * Test Antares\Tester\Adapter\MessageAdapter::add() method.
     *
     * @test
     */
    public function testAdd()
    {
        $stub     = new Stub;
        $stub->setDomain('default');
        $expected = [
            ['message' => 'message', 'code' => 500, 'type' => 'error', 'descriptor' => false]
        ];
        $this->assertInstanceOf('Antares\Tester\Adapter\MessageAdapter', $stub->add('message', 500, 'error'));
        $this->assertEquals($stub->messages(), $expected);
    }

    /**
     * Test Antares\Tester\Adapter\MessageAdapter::setDomain() method.
     *
     * @test
     */
    public function testSetDomain()
    {
        $stub = new Stub;
        $this->assertInstanceOf('Antares\Tester\Adapter\MessageAdapter', $stub->setDomain('products'));
        $this->assertInstanceOf('Antares\Tester\Adapter\MessageAdapter', $stub->setDomain('default'));
    }

    /**
     * Test Antares\Tester\Adapter\MessageAdapter::messages() method.
     *
     * @test
     */
    public function testMessages()
    {
        $stub     = new Stub;
        $stub->setDomain('default');
        $this->assertEmpty($stub->messages());
        $expected = [
            ['message' => 'message', 'code' => 500, 'type' => 'error', 'descriptor' => false]
        ];
        $stub->add('message', 500, 'error');
        $this->assertEquals($stub->messages(), $expected);
    }

}
