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

namespace Antares\Config\TestCase;

use Mockery as m;
use Antares\Config\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{

    public function testHasGroupIndicatesIfConfigGroupExists()
    {
        $config = $this->getRepository();
        $config->getLoader()->shouldReceive('exists')->once()->with('group', 'namespace')->andReturn(false);
        $this->assertFalse($config->hasGroup('namespace::group'));
    }

    public function testHasOnTrueReturnsTrue()
    {
        $config  = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'app', null)->andReturn($options);
        $config->getLoader()->shouldReceive('exists')->once()->with('app/bing')->andReturn(false);

        $this->assertTrue($config->has('app.bing'));
        $this->assertTrue($config->get('app.bing'));
    }

    public function testGetReturnsBasicItems()
    {
        $config  = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'app', null)->andReturn($options);
        $config->getLoader()->shouldReceive('exists')->twice()->with('app/foo')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->once()->with('app/baz')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->once()->with('app/code')->andReturn(false);

        $this->assertEquals('bar', $config->get('app.foo'));
        $this->assertNull($config->get('app.foo.bar'));
        $this->assertEquals('breeze', $config->get('app.baz.boom'));
        $this->assertEquals('blah', $config->get('app.code', 'blah'));
        $this->assertEquals('blah', $config->get('app.code', function () {
                    return 'blah';
                }));
    }

    public function testEntireArrayCanBeReturned()
    {
        $config  = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'app', null)->andReturn($options);

        $this->assertEquals($options, $config->get('app'));
    }

    public function testLoaderGetsCalledCorrectForNamespaces()
    {
        $config  = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'options', 'namespace')->andReturn($options);

        $this->assertEquals('bar', $config->get('namespace::options.foo'));
        $this->assertEquals('breeze', $config->get('namespace::options.baz.boom'));
        $this->assertEquals('blah', $config->get('namespace::options.code', 'blah'));
        $this->assertEquals('blah', $config->get('namespace::options.code', function () {
                    return 'blah';
                }));
    }

    public function testNamespacedAccessedAndPostNamespaceLoadEventIsFired()
    {
        $config  = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'options', 'namespace')->andReturn($options);
        $config->afterLoading('namespace', function ($repository, $group, $items) {
            $items['dayle'] = 'rees';

            return $items;
        });

        $this->assertEquals('bar', $config->get('namespace::options.foo'));
        $this->assertEquals('breeze', $config->get('namespace::options.baz.boom'));
        $this->assertEquals('blah', $config->get('namespace::options.code', 'blah'));
        $this->assertEquals('blah', $config->get('namespace::options.code', function () {
                    return 'blah';
                }));
        $this->assertEquals('rees', $config->get('namespace::options.dayle'));
    }

    public function testLoaderUsesNamespaceAsGroupWhenUsingPackagesAndGroupDoesntExist()
    {
        $config  = $this->getRepository();
        $options = $this->getDummyOptions();
        $config->getLoader()->shouldReceive('addNamespace')->with('namespace', __DIR__);
        $config->getLoader()->shouldReceive('cascadePackage')->andReturnUsing(function ($env, $package, $group, $items) {
            return $items;
        });
        $config->getLoader()->shouldReceive('exists')->once()->with('foo', 'namespace')->andReturn(false);
        $config->getLoader()->shouldReceive('exists')->once()->with('baz', 'namespace')->andReturn(false);
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'config', 'namespace')->andReturn($options);

        $config->package('foo/namespace', __DIR__);
        $this->assertEquals('bar', $config->get('namespace::foo'));
        $this->assertEquals('breeze', $config->get('namespace::baz.boom'));
    }

    public function testItemsCanBeSet()
    {
        $config = $this->getRepository();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'foo', null)->andReturn(['name' => 'dayle']);
        $config->getLoader()->shouldReceive('exists')->once()->with('foo/name')->andReturn(false);

        $config->set('foo.name', 'taylor');
        $this->assertEquals('taylor', $config->get('foo.name'));

        $config = $this->getRepository();
        $config->getLoader()->shouldReceive('load')->once()->with('production', 'foo', 'namespace')->andReturn(['name' => 'dayle']);

        $config->set('namespace::foo.name', 'taylor');
        $this->assertEquals('taylor', $config->get('namespace::foo.name'));
    }

    public function testPackageRegistersNamespaceAndSetsUpAfterLoadCallback()
    {
        $this->markTestIncomplete('This test has not been implemented yet because of deprecated test method');

        $config    = $this->getMock('\Antares\Config\Repository', ['addNamespace'], [m::mock('\Antares\Config\LoaderInterface'), 'production']);
        $config->expects($this->once())->method('addNamespace')->with($this->equalTo('rees'), $this->equalTo(__DIR__));
        $config->getLoader()->shouldReceive('cascadePackage')->once()->with('production', 'dayle/rees', 'group', ['foo'])->andReturn(['bar']);
        $config->package('dayle/rees', __DIR__);
        $afterLoad = $config->getAfterLoadCallbacks();
        $results   = call_user_func($afterLoad['rees'], $config, 'group', ['foo']);
        $this->assertEquals(['bar'], $results);
    }

    protected function getRepository()
    {
        return new Repository(m::mock('\Antares\Config\LoaderInterface'), 'production');
    }

    protected function getDummyOptions()
    {
        return ['foo' => 'bar', 'baz' => ['boom' => 'breeze'], 'bing' => true];
    }

}
