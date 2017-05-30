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


namespace Antares\Asset\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AsseticBuildCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name        = 'assetic:build';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build assets with Assetic, from views';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->info('Starting Assetic building..');
        $app     = $this->laravel;
// Boot assetic
        $assetic = $app['assetic'];
        $helper  = $app['assetic.dumper'];
        if (isset($app['twig'])) {
            $helper->addTwigAssets();
        }
        $helper->dumpAssets();
        $this->info('Done building assets!');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }

}
