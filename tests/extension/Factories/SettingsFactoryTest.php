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

namespace Antares\Extension\Factories\TestCase;

use Antares\Extension\Factories\SettingsFactory;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;

class SettingsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Mockery\MockInterface
     */
    protected $filesystem;

    public function setUp()
    {
        parent::setUp();

        $this->filesystem = m::mock(Filesystem::class);
    }

    /**
     * @return SettingsFactory
     */
    protected function getFactory()
    {
        return new SettingsFactory($this->filesystem);
    }

    public function testCreateFromDataMethodAsEmpty()
    {
        $configData = [];

        $settings = $this->getFactory()->createFromData($configData);

        $this->assertEquals([], $settings->getData());
        $this->assertEquals([], $settings->getValidationRules());
        $this->assertEquals([], $settings->getValidationPhrases());
    }

    public function testCreateFromDataMethodWithConfig()
    {
        $data = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $rules = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $phrases = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $configData = compact('data', 'rules', 'phrases');

        $settings = $this->getFactory()->createFromData($configData);

        $this->assertEquals($data, $settings->getData());
        $this->assertEquals($rules, $settings->getValidationRules());
        $this->assertEquals($phrases, $settings->getValidationPhrases());
    }

    public function testCreateFromConfig()
    {
        $data = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $rules = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $phrases = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $dumpConfigPath = 'foo/bar';
        $configData     = compact('data', 'rules', 'phrases');

        $this->filesystem->shouldReceive('getRequire')
                ->once()
                ->with($dumpConfigPath)
                ->andReturn($configData)
                ->getMock();

        $settings = $this->getFactory()->createFromConfig($dumpConfigPath);

        $this->assertEquals($data, $settings->getData());
        $this->assertEquals($rules, $settings->getValidationRules());
        $this->assertEquals($phrases, $settings->getValidationPhrases());
    }

}
