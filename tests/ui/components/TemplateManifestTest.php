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
 * @package    Widgets
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Widgets\Tests;

use Antares\UI\UIComponents\TemplateManifest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Fluent;
use Antares\Testing\TestCase;
use Mockery as m;

class TemplateManifestTest extends TestCase
{

    /**
     * @var TemplateManifest
     */
    protected $stub;

    /**
     * set of fixtures 
     * 
     * @var array
     */
    protected $fixtures = [
        'expected' => '{
                                "package": "green",
                                "name": "Green Widget Template",
                                "description": "This is green widget template",
                                "author": "Łukasz Cirut",
                                "url": "https://modulesgarden.com/docs",
                                "type": [],
                                "autoload": []
                            }',
        'config'   => [
            'public_path'      => 'widgets/templates',
            'preview_pattern'  => 'screenshot.png',
            'preview_default'  => 'packages/antares/widgets/img/screenshot.png',
            'manifest_pattern' => 'template.json',
            'indexes_path'     => 'resources/views/templates',
            'dir'              => ''
        ]
    ];

    /**
     * @inherits
     */
    public function setUp()
    {
        parent::setUp();
        $fileSystem = m::mock(Filesystem::class);
        $fileSystem
                ->shouldReceive('exists')
                ->withAnyArgs()
                ->andReturn(true)
                ->shouldReceive('get')
                ->withAnyArgs()
                ->andReturn($this->fixtures['expected']);
        $config     = $this->fixtures['config'] + ['public' => $this->app['path.public']];
        $this->stub = new TemplateManifest($fileSystem, $config, __DIR__ . '/Fixtures/Widgets');
    }

    /**
     * test constructing
     * 
     * @tests
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(TemplateManifest::class, $this->stub);
    }

    /**
     * test items
     * 
     * @tests
     */
    public function testItems()
    {
        $items = $this->stub->items();
        $this->assertInstanceOf(Fluent::class, $items);
        $this->assertTrue(!empty($items->getAttributes()));
    }

    /**
     * test get
     * 
     * @tests
     */
    public function testGet()
    {
        $this->assertEquals('green', $this->stub->get('package'));
    }

}
