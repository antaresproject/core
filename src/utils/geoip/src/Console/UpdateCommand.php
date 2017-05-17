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


namespace Antares\GeoIP\Console;

use Antares\Config\Repository;
use Illuminate\Console\Command;
use Antares\GeoIP\GeoIPUpdater;

class UpdateCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'geoip:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update geoip database files to the latest version';

    /**
     * @var \Torann\GeoIP\GeoIPUpdater
     */
    protected $geoIPUpdater;

    /**
     * Create a new console command instance.
     *
     * @param \Illuminate\Config\Repository $config
     */
    public function __construct(Repository $config)
    {
        parent::__construct();

        $this->geoIPUpdater = new GeoIPUpdater($config);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $result = $this->geoIPUpdater->update();

        if (!$result) {
            $this->error('Update failed!');

            return;
        }

        $this->info('New update file (' . $result . ') installed.');
    }

}
