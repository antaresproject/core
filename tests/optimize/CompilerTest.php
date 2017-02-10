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
 namespace Antares\Optimize\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Antares\Optimize\Compiler;

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test \Antares\Optimize\Compiler::run() method.
     *
     * @test
     */
    public function testRunMethod()
    {
        $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $files  = m::mock('\Illuminate\Filesystem\Filesystem');
        $path   = '/var/www/laravel/vendor';

        $components = [
            'antares/asset' => [
                'src/AssetServiceProvider',
                'src/NoneExistClass',
            ],
            'antares/foo' => [
                'src/FooServiceProvider',
            ],
        ];

        $added = [
            "{$path}/antares/asset/src/AssetServiceProvider.php",
            "app/Foobar.php",
        ];
        $missing  = [
            "{$path}/antares/asset/src/NoneExistClass.php",
        ];

        $config->shouldReceive('get')->once()->with('compile.files', [])
                ->andReturn([
                    "app/Foobar.php",
                ])
            ->shouldReceive('set')->once()->with('compile.files', $added)->andReturn(null);
        $files->shouldReceive('isDirectory')->once()
                ->with("{$path}/antares/asset")->andReturn(true)
            ->shouldReceive('isDirectory')->once()
                ->with("{$path}/antares/foo")->andReturn(false)
            ->shouldReceive('exists')->once()
                ->with("{$path}/antares/asset/src/AssetServiceProvider.php")->andReturn(true)
            ->shouldReceive('exists')->once()
                ->with("{$path}/antares/asset/src/NoneExistClass.php")->andReturn(false);

        $stub     = new Compiler($config, $files, $path, $components);
        $compiled = $stub->run();

        $expected = new Fluent([
            'added'   => $added,
            'missing' => $missing,
        ]);

        $this->assertEquals($expected, $compiled);
    }
}
