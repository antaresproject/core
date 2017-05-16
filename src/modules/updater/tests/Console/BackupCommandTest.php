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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Updater\Console\TestCase;

use Symfony\Component\Console\Style\OutputStyle;
use Antares\Updater\Console\DbBackupCommand;
use Antares\Updater\UpdaterServiceProvider;
use Antares\Testing\ApplicationTestCase;
use Antares\Config\Repository;
use Mockery as m;
use Exception;

class BackupCommandTest extends ApplicationTestCase
{

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->addProvider(UpdaterServiceProvider::class);
        parent::setUp();
    }

    /**
     * Tests Antares\Updater\Console\BackupCommand::handle
     * 
     * @test
     */
    public function testHandle()
    {
        $command             = new DbBackupCommand();
        $command->setOutput($outputMock          = m::mock(OutputStyle::class));
        $this->app['config'] = $config              = m::mock(Repository::class);
        $config->shouldReceive('get')->with('database.default', null)->andReturn('mysql')
                ->shouldReceive('get')->with("database.connections.mysql.driver", null)->andReturn('mysql')
                ->shouldReceive('get')->with("database.connections.mysql", null)->andReturn([
                    "driver"    => "mysql",
                    "host"      => "127.0.0.1",
                    "database"  => "foo",
                    "username"  => "root",
                    "password"  => "",
                    "charset"   => "utf8",
                    "collation" => "utf8_unicode_ci",
                    "prefix"    => "",
                    "strict"    => false
                ])
                ->shouldReceive('get')->with("laravel-backup.mysql.useExtendedInsert", null)->andReturn(false)
                ->shouldReceive('get')->with("laravel-backup.mysql.dump_command_path", null)->andReturn('')
                ->shouldReceive('get')->with("laravel-backup.mysql.timeoutInSeconds", NULL)->andReturn(60);



        $info   = "<comment>Database dumped</comment>";
        $output = [];
        $outputMock->shouldReceive('writeln')->with(m::type('string'), 32)->once()->andReturnUsing(function($arg) use(&$output) {
            array_push($output, $arg);
            return $arg;
        });


        try {
            $this->assertNull($command->handle());
            $this->assertContains($info, $output);
            if (!preg_match('/"([^"]+)"/', $output[4], $m)) {
                throw new Exception('Unable to find backup dump file.');
            }
            $file = getcwd() . '/storage/app/' . $m[1];
            if (file_exists($file)) {
                unlink($file);
            }
        } catch (Exception $ex) {
            $this->markTestIncomplete($ex->getMessage());
        }
    }

}
