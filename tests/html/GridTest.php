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
 namespace Antares\Html\TestCase;

use Antares\Html\Grid;
use Illuminate\Container\Container;

class GridTest extends \PHPUnit_Framework_TestCase
{
    public function testMetaData()
    {
        $app  = new Container();
        $stub = new GridStub($app);

        $refl = new \ReflectionObject($stub);
        $meta = $refl->getProperty('meta');
        $meta->setAccessible(true);

        $this->assertEquals([], $meta->getValue($stub));

        $stub->set('foo.bar', 'foobar');
        $stub->set('foo.hello', 'world');

        $this->assertEquals(['foo' => ['bar' => 'foobar', 'hello' => 'world']], $meta->getValue($stub));
        $this->assertEquals('foobar', $stub->get('foo.bar'));
        $this->assertNull($stub->get('foobar'));

        $stub->forget('foo.bar');

        $this->assertEquals(['foo' => ['hello' => 'world']], $meta->getValue($stub));
        $this->assertNull($stub->get('foo.bar'));
    }
}

class GridStub extends Grid
{
    }
