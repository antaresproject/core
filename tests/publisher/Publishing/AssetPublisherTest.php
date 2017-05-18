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

namespace Antares\Publisher\Publishing\TestCase;

use Antares\Publisher\Publishing\AssetPublisher;
use Antares\Testbench\ApplicationTestCase;
use Mockery as m;

class AssetPublisherTest extends ApplicationTestCase
{

    public function testBasicPathPublishing()
    {
        $pub   = new AssetPublisher($files = m::mock('Illuminate\Filesystem\Filesystem'), __DIR__);
        $files->shouldReceive('isDirectory')->once()->with(__DIR__ . '/packages/antares/bar')->andReturn(true)
                ->shouldReceive('isDirectory')->withAnyArgs()->andReturn(true)
                ->shouldReceive('allFiles')->once()->andReturn([]);



        $this->app['files'] = $files              = m::mock('\Illuminate\Filesystem\Filesystem');

        $this->app['antares.asset.publisher'] = $publisher                            = m::mock(\Antares\Asset\AssetPublisher::class);
        $publisher->shouldReceive('publishAndPropagate')->once()->andReturn(true);
        $this->assertTrue($pub->publish('bar', 'foo'));
    }

    public function testPackageAssetPublishing()
    {
        $pub   = new AssetPublisher($files = m::mock('Illuminate\Filesystem\Filesystem'), __DIR__);
        $pub->setPackagePath(__DIR__ . '/vendor');
        $files->shouldReceive('isDirectory')->withAnyArgs()->andReturn(true)
                ->shouldReceive('allFiles')->once()->andReturn([]);

        $this->app['antares.asset.publisher'] = $publisher                            = m::mock(\Antares\Asset\AssetPublisher::class);
        $publisher->shouldReceive('publishAndPropagate')->once()->andReturn(true);

        $this->assertTrue($pub->publishPackage('foo'));

        $pub    = new AssetPublisher($files2 = m::mock('Illuminate\Filesystem\Filesystem'), __DIR__);
        $files2->shouldReceive('isDirectory')->withAnyArgs()->andReturn(false);


        try {
            $pub->publishPackage('foo', __DIR__ . '/custom-packages');
        } catch (\Exception $ex) {
            $this->assertSame("Assets not found.", $ex->getMessage());
        }
    }

}
