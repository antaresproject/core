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

namespace Antares\Extension\TestCase;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Factories\ExtensionFactory;
use Antares\Extension\FilesystemFinder;
use Antares\Extension\Repositories\ConfigRepository;
use Antares\Extension\Validators\ExtensionValidator;
use Composer\Package\PackageInterface;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;

class FilesystemFinderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Mockery\MockInterface
     */
    protected $application;

    /**
     * @var \Mockery\MockInterface
     */
    protected $configRepository;

    /**
     * @var \Mockery\MockInterface
     */
    protected $extensionValidator;

    /**
     * @var \Mockery\MockInterface
     */
    protected $extensionFactory;

    /**
     * @var \Mockery\MockInterface
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $providersPath = '';

    public function setUp()
    {
        parent::setUp();

        $this->configRepository   = m::mock(ConfigRepository::class);
        $this->extensionValidator = m::mock(ExtensionValidator::class);
        $this->extensionFactory   = m::mock(ExtensionFactory::class);
        $this->filesystem         = m::mock(Filesystem::class);

        $this->configRepository
                ->shouldReceive('getRootPath')
                ->andReturn('/root/of/path')
                ->getMock();
    }

    /**
     * @return FilesystemFinder
     */
    protected function getFinderInstance()
    {
        return new FilesystemFinder($this->configRepository, $this->extensionValidator, $this->extensionFactory, $this->filesystem);
    }

    public function testFindExtensionWithException()
    {
        $this->setExpectedException(ExtensionException::class);

        $paths = [
            'dummy/component/path',
        ];

        $this->configRepository
                ->shouldReceive('getPaths')
                ->once()
                ->andReturn($paths)
                ->getMock();

        $this->filesystem
                ->shouldReceive('glob')
                ->once()
                ->andReturn(false)
                ->getMock();

        $this->getFinderInstance()->findExtensions();
    }

    public function testFindExtension()
    {
        $paths = [
            'dummy/component_one/path',
        ];

        $composerPaths = [
            '/root/of/path/dummy/component_one/path/composer.json', // it will be invalid
            '/root/of/path/dummy/component_two/path/composer.json', // it will be valid
        ];

        $this->configRepository
                ->shouldReceive('getPaths')
                ->once()
                ->andReturn($paths)
                ->getMock();

        $this->filesystem
                ->shouldReceive('glob')
                ->once()
                ->andReturn($composerPaths)
                ->getMock();

        $validPackage = m::mock(PackageInterface::class)
                ->shouldReceive('getType')
                ->once()
                ->andReturn('antaresproject-component')
                ->getMock();

        $invalidPackage = m::mock(PackageInterface::class)
                ->shouldReceive('getType')
                ->once()
                ->andReturn('some-not-supported-type')
                ->getMock();

        $extension = m::mock(ExtensionContract::class);

        $this->extensionFactory
                ->shouldReceive('getComposerPackage')
                ->once()
                ->with($composerPaths[0])
                ->andReturn($invalidPackage)
                ->shouldReceive('getComposerPackage')
                ->once()
                ->with($composerPaths[1])
                ->andReturn($validPackage)
                ->getMock()
                ->shouldReceive('create')
                ->once()
                ->with($composerPaths[1])
                ->andReturn($extension)
                ->getMock();

        $this->extensionValidator
                ->shouldReceive('isValid')
                ->once()
                ->with($extension)
                ->andReturn(true)
                ->getMock();

        $extensions = $this->getFinderInstance()->findExtensions();

        $this->assertCount(1, $extensions->all());
    }

}
