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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation\Model\TestCase;

use Antares\Automation\Console\SyncCommand;
use Antares\Automation\Memory\JobsMemory;
use Antares\Memory\MemoryManager;
use Antares\Memory\Provider;
use Antares\Model\Component;
use Antares\Support\Collection;
use Antares\Testing\TestCase;
use Mockery as m;
use stdClass;
use Symfony\Component\Console\Style\OutputStyle;

class SyncCommandTest extends TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Tests Antares\Automation\Console\SyncCommand::handle
     */
    public function testHandle()
    {
        $this->app[Component::class] = $componentMock               = m::mock(Component::class);
        $mockObject                  = new stdClass();
        $mockObject->name            = 'acl_antares';
        $mockObject->id              = 1;
        $collection                  = new Collection([
            $mockObject
        ]);
        $this->app[Provider::class]  = $mProvider                   = m::mock(Provider::class);
        $this->app['antares.memory'] = $memoryManagerMock           = m::mock(MemoryManager::class);
        $memoryManagerMock->shouldReceive('make')->with('component')->once()->andReturn($mProvider)
                ->shouldReceive('make')->with('jobs')->once()->andReturn($jobsMemory                  = m::mock(JobsMemory::class));


        $mProvider->shouldReceive('get')->with('extensions.active')->once()->andReturn([
            "components/automation" => [
                "path"        => "base::src/components/automation",
                "source-path" => "base::src/components/automation",
                "name"        => "automation",
                "full_name"   => "Automation Manager",
                "description" => "Automation Manager Antares",
                "author"      => "Łukasz Cirut",
                "url"         => "https://antares.com",
                "version"     => "0.5",
                "config"      => [],
                "autoload"    => [],
                "provides"    => [
                    "Antares\Automation\AutomationServiceProvider",
                    "Antares\Automation\CommandServiceProvider",
                    "Antares\Automation\ScheduleServiceProvider"
                ],
            ]
        ]);

        $componentMock->shouldReceive('all')->withNoArgs()->andReturn($collection);

        $command    = new SyncCommand();
        $command->setOutput($outputMock = m::mock(OutputStyle::class));
        $info       = '<info>No jobs found.</info>';
        $outputMock->shouldReceive('writeln')->withAnyArgs()->once()->andReturn($info);

        $this->assertNull($command->handle());
    }

    /**
     * Tests Antares\Automation\Console\SyncCommand::setOutput
     */
    public function testSetOutput()
    {
        $command    = new SyncCommand();
        $this->assertInstanceOf(SyncCommand::class, $command->setOutput($outputMock = m::mock(OutputStyle::class)));
    }

}
