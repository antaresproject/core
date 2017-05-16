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






namespace Antares\Updater\Filesystem;

use Antares\Updater\Contracts\Resolver as ResolverContract;
use Illuminate\Support\Facades\Log;
use Exception;

class Resolver extends AbstractResolver implements ResolverContract
{

    /**
     * validate compressed file with update script
     */
    protected function validate()
    {
        try {
            if (!$this->isDifferent()) {
                return true;
            }
            $config   = config('antares/updater::resolver');
            $manifest = $this->path . DIRECTORY_SEPARATOR . array_get($config, 'pattern');


            if (!$this->filesystem->exists($manifest)) {
                throw new Exception('Unable to update system. Manifest file has not been found in migration package.', 504);
            }
            $content = json_decode($this->filesystem->get($manifest), 'json');

            $requirements = array_get($config, 'requirements');

            foreach ($requirements as $key) {
                if (!array_key_exists($key, $content)) {
                    throw new Exception(sprintf('Unable to update system. Manifest file has not a valid key-value pair for %s entity.', $key), 505);
                }
            }
            if ($this->version !== array_get($content, 'version')) {
                throw new Exception(sprintf('Unable to update system. Update is not consistent with the reported version %s.', $this->version), 506);
            }
            return true;
        } catch (Exception $ex) {
            Log::emergency($ex);
            $this->messages = array_add($this->messages, $ex->getCode(), $ex->getMessage());
            $this->hasError = true;
        }
        return false;
    }

    /**
     * check whether actual version is different than updating
     * 
     * @return boolean
     */
    protected function isDifferent()
    {
        return $this->model->actual()->first()->app_version != $this->version;
    }

    /**
     * decompress migration file with files mapping
     */
    public function resolve()
    {
        try {
            $downloaded = $this->adapter->download($this->path);
            if (is_null($downloaded) or $downloaded == false) {
                throw new Exception('Unable to download migration package.', 504);
            }
            $directory = $this->adapter->decompress($downloaded);
            if (!is_dir($directory)) {
                throw new Exception('Invalid decompressed migration structure. Please check whether migration file has valid structure.', 503);
            }
            $this->path = $directory;
        } catch (Exception $e) {
            Log::emergency($e);
            $this->messages = array_add($this->messages, $e->getCode(), $e->getMessage());
            $this->hasError = true;
        }
        return $this;
    }

    /**
     * run migration
     */
    public function migrate()
    {
        try {
            $this->migrator->migrate($this->path);
            return true;
        } catch (Exception $e) {
            Log::emergency($e);
            $this->messages = array_add($this->messages, $e->getCode(), $e->getMessage());
            $this->hasError = true;
            return false;
        }
    }

}
