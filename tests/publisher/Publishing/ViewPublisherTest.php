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

use Antares\Publisher\Publishing\ViewPublisher;
use Antares\Testbench\ApplicationTestCase;
use Mockery as m;

class ViewPublisherTest extends ApplicationTestCase
{

    public function testPackageViewPublishing()
    {
        $pub   = new ViewPublisher($files = m::mock('\Illuminate\Filesystem\Filesystem'), __DIR__);
        $pub->setPackagePath(__DIR__ . '/vendor');
        $files->shouldReceive('isDirectory')->twice()->withAnyArgs()->andReturn(true)
                ->shouldReceive('allFiles')->once()->andReturn([]);

        $this->app['antares.asset.publisher'] = $publisher                            = m::mock(\Antares\Asset\AssetPublisher::class);
        $publisher->shouldReceive('publishAndPropagate')->once()->andReturn(true);

        $this->assertTrue($pub->publishPackage('foo/bar'));

        $pub    = new ViewPublisher($files2 = m::mock('\Illuminate\Filesystem\Filesystem'), __DIR__);
        $files2->shouldReceive('isDirectory')->twice()->withAnyArgs()->andReturn(false);

        try {
            $pub->publishPackage('foo/bar', __DIR__ . '/custom-packages');
        } catch (\Exception $ex) {
            $this->assertSame('Views not found.', $ex->getMessage());
        }
    }

}
