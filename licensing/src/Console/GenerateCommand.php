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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Licensing\Console;

use Antares\Licensing\Model\LicenseTypes;
use Illuminate\Console\Command as BaseCommand;

class GenerateCommand extends BaseCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'license:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates license file.';

    /**
     * signature definition
     *
     * @var String 
     */
    protected $signature = 'license:generate {expiration : The date of license expiration in format DD.MM.YYYY} {hostname : The name of the host} {type : The name of license type: trial, lite, standard, ultimate, enterprise}';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $defaultStart = date('d.m.Y');
        $start        = $this->ask("Please provide license start date", $defaultStart);

        if (!$this->validateStart($start)) {
            return;
        }

        $defaultExpiration = date('d.m.Y', strtotime("+30 days"));
        $expiration        = $this->ask("Please provide license expiration date", $defaultExpiration);

        if (!$this->validateExpiration($expiration)) {
            return;
        }
        $defaultHostname = 'localhost';
        $hostname        = $this->ask("Please provide license hostname", $defaultHostname);

        $defaultType = 'trial';
        $types       = implode(', ', array_values(LicenseTypes::all()->pluck('name', 'id')->toArray()));
        $type        = $this->ask("Please provide license type ({$types})", $defaultType);

        if (!$this->validateType($type)) {
            return;
        }

        $expireIn  = strtotime($expiration) - time();
        $generator = app('antares.license.generator');

        $filesystem = app('Illuminate\Filesystem\Filesystem');

        $key = $generator->generateKey();
        if (!strlen($key) > 0) {
            $this->info('Unable to create new license key file.');
            return;
        }

        $date    = str_replace('.', '_', $expiration);
        $keyPath = storage_path("license/license_{$hostname}_{$date}_{$type}.key");

        if ($filesystem->put($keyPath, $key)) {
            $this->info("License key file saved in {$keyPath}.");
        }
        $license = $generator->generate($hostname, strtotime($start), $expireIn, $type, $key);

        if (!strlen($license) > 0) {
            $this->info('Unable to create new license file.');
            return;
        }

        $path = storage_path("license/license_{$hostname}_{$date}_{$type}.cert");
        if ($filesystem->put($path, $license)) {
            $this->info("New license file saved in {$path}.");
            return;
        }
    }

    /**
     * validates start date
     * 
     * @param String $start
     * @return boolean
     */
    protected function validateStart($start)
    {
        if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1]).(0[1-9]|1[0-2]).[0-9]{4}$/", $start)) {
            $this->info('Start date has invalid format. Only DD.MM.YYYY date format is accepted.');
            return false;
        }
        return true;
    }

    /**
     * validates expiration date
     * 
     * @param String $expiration
     * @return boolean
     */
    protected function validateExpiration($expiration)
    {
        if (!preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1]).(0[1-9]|1[0-2]).[0-9]{4}$/", $expiration)) {
            $this->info('Expiration date has invalid format. Only DD.MM.YYYY date format is accepted.');
            return false;
        }
        if (strtotime($expiration) <= time()) {
            $this->info('Expiration date must be greater than today\'s date.');
            return false;
        }
        return true;
    }

    /**
     * validates name of license type
     * 
     * @param String $type
     * @return boolean
     */
    protected function validateType($type)
    {
        $types = config('antares/licensing::types');
        if (!in_array($type, $types)) {
            $this->info(sprintf('Name of type called "%s" is not valid. Only {%s} can be used.', $type, implode(', ', $types)));
            return false;
        }
        return true;
    }

}
