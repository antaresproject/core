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






namespace Antares\Updater\Strategy\Adapter;

use Illuminate\Database\Seeder as LaravelSeeder;
use SplFileInfo;
use Illuminate\Support\Str;

class SeederAdapter extends AbstractMigrator
{

    /**
     * which files in seed directory
     * 
     * @param String $path
     * @return array
     */
    protected function files($path)
    {
        return $this->files->glob($path . '/*.php');
    }

    /**
     * seed migration
     * 
     * @param String $path
     * @return \Antares\Updater\Strategy\Adapter\SeederAdapter
     */
    public function seed($path)
    {
        $this->note('SEEDER: Prepare to seed package repository');
        $files = $this->files($path);
        foreach ($files as $file) {
            $this->note(sprintf('Run seeding of %s', $file));
            $this->resolve($file)->run();
        }
        return $this;
    }

    /**
     * rollback seed transaction
     * 
     * @param String $path
     * @return \Antares\Updater\Strategy\Adapter\SeederAdapter
     */
    public function down($path)
    {
        $files = $this->files($path);
        foreach ($files as $file) {
            $this->note(sprintf('Run rollback of seeding %s', $file));
            $this->resolve($file)->down();
        }
        return $this;
    }

    /**
     * resolve seed instance
     * 
     * @param SplFileInfo $file
     * @return LaravelSeeder
     */
    protected function resolve($file)
    {
        $this->files->requireOnce($file);
        $spl   = new SplFileInfo($file);
        $class = Str::studly(substr($spl->getFilename(), 0, -4));
        return new $class;
    }

}
