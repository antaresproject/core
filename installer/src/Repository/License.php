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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Installation\Repository;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use function storage_path;
use function app;
use Exception;

class License
{

    /**
     * uploads and creates license file
     * 
     * @param Request $request
     * @return boolean
     */
    public function uploadLicense(Request $request)
    {
        try {

            /* @var $file UploadedFile */
            $file = $request->file('license_file');
            if (is_null($file)) {
                throw new Exception('Invalid license key provided.');
            }
            $this->clearDirectory();
            $filename = $file->getClientOriginalName();
            $request->file('license_file')->move(storage_path('license'), $filename);

            $keyFilename = str_replace($file->getClientOriginalExtension(), 'key', $filename);

            /* @var $filesystem Filesystem */
            $filesystem = app(Filesystem::class);
            $filesystem->put(storage_path("license/{$keyFilename}"), $request->get('license_key'));
            app('antares.memory')->make('runtime')->push('instance_key', $request->get('license_key'));
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * cleans license directory
     * 
     * @return void
     */
    protected function clearDirectory()
    {
        /* @var $filesystem Filesystem */
        $filesystem = app(Filesystem::class);
        $files      = $filesystem->allFiles(storage_path('license'));
        if (empty($files)) {
            return;
        }
        foreach ($files as $file) {
            if ($file->getFilename() == '.gitignore') {
                continue;
            }
            $filesystem->delete($file);
        }
    }

}
