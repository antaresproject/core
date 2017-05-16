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






namespace Antares\Updater\Strategy\Sandbox;

use Antares\Updater\Contracts\Requirements as RequirementsContract;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Antares\Memory\MemoryManager;
use Exception;

class Requirements extends AbstractStrategy implements RequirementsContract
{

    /**
     * config container
     *
     * @var array
     */
    protected $config;

    /**
     * constructing
     * 
     * @param Repository $config
     * @param MemoryManager $memory
     */
    public function __construct(Repository $config)
    {
        $this->config = $config->get('antares/updater::sandbox');
    }

    /**
     * verify whether there is sufficient space
     * 
     * @return Requirements
     * @throws Exception
     */
    protected function space()
    {

        $bytes   = disk_free_space(base_path());
        $space   = array_get($this->config, 'requirements.space.min');
        $current = intval($bytes / 1048576);

        if ($current < $space) {
            throw new Exception(sprintf('Not enough disk space to create sandbox instance. Min required space is %s, current is %s', $space, $current));
        }
        return $this;
    }

    /**
     * verify whether current database user is allowed to create new database instance
     * 
     * @return Requirements
     * @throws Exception
     */
    protected function database()
    {
        $default   = config('database.default');
        $username  = config("database.connections.{$default}.username");
        $pattern   = "/GRANT ALL PRIVILEGES ON *.* TO '{$username}'@'(.*)' WITH GRANT OPTION/";
        $grants    = DB::select('SHOW GRANTS FOR CURRENT_USER');
        $canCreate = false;
        foreach ($grants as $grant) {
            if (preg_match($pattern, current($grant))) {
                $canCreate = true;
                break;
            }
        }
        if (!$canCreate) {
            throw new Exception('Current user is not allowed to create new database instance.');
        }
        return $this;
    }

    /**
     * checking whether builds directory exists and is writable
     * 
     * @return Requirements
     * @throws Exception
     */
    protected function permissions()
    {
        try {
            $buildsPath = array_get($this->config, 'requirements.permissions.path');
            if (!is_dir($buildsPath)) {
                $filesystem = app(Filesystem::class);
                $filesystem->makeDirectory($buildsPath, 0777, true);
            }
            if (!is_writable($buildsPath)) {
                throw new Exception('Builds directory is not writable.');
            }
        } catch (Exception $ex) {
            throw new Exception(sprintf('Builds directory ( %s ) is not writable.', $buildsPath));
        }

        return $this;
    }

    /**
     * checking instance requirements 
     * 
     * @return boolean
     */
    public function validate()
    {
        try {
            $this->space()->database()->permissions();
            return true;
        } catch (Exception $e) {
            Log::emergency($e);
            $this->note($e->getMessage());
            $this->hasError = true;
            return false;
        }
    }

}
