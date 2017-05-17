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

use Antares\Notifier\Message;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test \Antares\Notifier\Message instance signature.
     *
     * @test
     */
    public function testInstanceSignature()
    {
        $stub = new Message();

        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub);
    }

    public function testCreateFactoryMethod()
    {
        $view    = 'foo.bar';
        $data    = ['data' => 'foo'];
        $subject = "Hello world";
        $stub    = Message::create($view, $data, $subject);

        $this->assertEquals($view, $stub->view);
        $this->assertEquals($data, $stub->data);
        $this->assertEquals($subject, $stub->subject);
        $this->assertEquals($view, $stub->getView());
        $this->assertEquals($data, $stub->getData());
        $this->assertEquals($subject, $stub->getSubject());
    }
}
