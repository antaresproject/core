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

namespace Antares\Messages\TestCase;

use Mockery as m;
use Antares\Messages\MessageBag;

class MessageBagTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Support\MessageBag::make() method.
     *
     * @test
     */
    public function testMakeMethod()
    {
        $session = m::mock('\Illuminate\Session\Store');

        $message = (new MessageBag())->setSessionStore($session);
        $message->add('welcome', 'Hello world');
        $message->setFormat();

        $this->assertInstanceOf('\Antares\Messages\MessageBag', $message);
        $this->assertEquals(['Hello world'], $message->get('welcome'));

        $message->add('welcome', 'Hi Foobar')->add('welcome', 'Heya Admin');
        $this->assertEquals(['Hello world', 'Hi Foobar', 'Heya Admin'], $message->get('welcome'));

        $this->assertEquals($session, $message->getSessionStore());
    }

    /**
     * Test Antares\Messages\MessageBag::save() method.
     *
     * @test
     */
    public function testSaveMethod()
    {
        $session = m::mock('\Illuminate\Session\Store');
        $session->shouldReceive('flash')->once()->andReturn(true);

        with(new MessageBag())->setSessionStore($session)->save();
    }

    /**
     * Test serializing and storing Antares\Messages\MessageBag over
     * Session.
     *
     * @test
     */
    public function testStoreMethod()
    {
        $session = m::mock('\Illuminate\Session\Store');

        $session->shouldReceive('flash')->once()->andReturn(true);

        $message = (new MessageBag())->setSessionStore($session);
        $message->add('hello', 'Hi World');
        $message->add('bye', 'Goodbye');

        $serialize = $message->serialize();

        $this->assertTrue(is_string($serialize));
        $this->assertContains('hello', $serialize);
        $this->assertContains('Hi World', $serialize);
        $this->assertContains('bye', $serialize);
        $this->assertContains('Goodbye', $serialize);

        $message->save();
    }

    /**
     * Test un-serializing and retrieving Antares\Messages\MessageBag over
     * Session.
     *
     * @test
     */
    public function testRetrieveMethod()
    {
        $session = m::mock('\Illuminate\Session\Store');
        $session->shouldReceive('has')->once()->andReturn(true)
                ->shouldReceive('pull')->once()
                ->andReturn('a:2:{s:5:"hello";a:1:{i:0;s:8:"Hi World";}s:3:"bye";a:1:{i:0;s:7:"Goodbye";}}');

        $retrieve = (new MessageBag())->setSessionStore($session)->retrieve();
        $retrieve->setFormat();

        $this->assertInstanceOf('\Antares\Messages\MessageBag', $retrieve);
        $this->assertEquals(['Hi World'], $retrieve->get('hello'));
        $this->assertEquals(['Goodbye'], $retrieve->get('bye'));
    }

    /**
     * Test un-serializing and extending Antares\Messages\MessageBag over
     * Session.
     *
     * @test
     */
    public function testExtendMethod()
    {
        $session = m::mock('\Illuminate\Session\Store');
        $session->shouldReceive('has')->once()->andReturn(true)
                ->shouldReceive('pull')->once()
                ->andReturn('a:1:{s:5:"hello";a:1:{i:0;s:8:"Hi World";}}');

        $callback = function ($msg) {
            $msg->add('hello', 'Hi Antares Platform');
        };

        $stub   = (new MessageBag())->setSessionStore($session);
        $output = $stub->extend($callback);

        $retrieve = $stub->retrieve();
        $retrieve->setFormat();

        $this->assertInstanceOf('\Antares\Messages\MessageBag', $output);
        $this->assertInstanceOf('\Antares\Messages\MessageBag', $retrieve);
        $this->assertEquals(['Hi World', 'Hi Antares Platform'], $retrieve->get('hello'));
    }

}
