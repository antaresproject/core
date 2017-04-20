<?php
/**
 * Created by PhpStorm.
 * User: Marcin Kozak
 * Date: 2017-04-11
 * Time: 14:02
 */

namespace Antares\Extension\TestCase;

use Mockery as m;
use Composer\Package\CompletePackageInterface;
use Antares\Extension\Contracts\Config\SettingsContract;
use Antares\Extension\Contracts\ExtensionContract;

trait ExtensionMockTrait {

    /**
     * @param $name
     * @return \Mockery\MockInterface
     */
    protected function buildExtensionMock($name) {
        $package = m::mock(CompletePackageInterface::class)
            ->shouldReceive('getName')
            ->andReturn($name)
            ->getMock();

        $settings = m::mock(SettingsContract::class)
            ->shouldReceive('getData')
            ->andReturn([])
            ->getMock();

        return m::mock(ExtensionContract::class)
            ->shouldReceive('getPackage')
            ->andReturn($package)
            ->getMock()
            ->shouldReceive('getSettings')
            ->andReturn($settings)
            ->getMock();
    }

}
