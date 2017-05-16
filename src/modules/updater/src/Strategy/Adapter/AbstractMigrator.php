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

use Illuminate\Filesystem\Filesystem;
use Antares\Updater\Traits\Note as NoteTrait;

abstract class AbstractMigrator
{

    use NoteTrait;

    /**
     * laravel migrator instance
     *
     * @var \Illuminate\Database\Migrations\Migrator 
     */
    protected $supportMigrator;

    /**
     * laravel filesystem
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * constructing
     */
    public function __construct(Filesystem $files)
    {
        $this->supportMigrator = app('migrator');
        $this->files           = $files;
    }

}
