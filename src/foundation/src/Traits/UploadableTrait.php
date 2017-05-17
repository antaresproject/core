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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */




namespace Antares\Foundation\Traits;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Facades\File;
use Antares\View\Theme\Manifest;
use Illuminate\Filesystem\Filesystem;
use ZipArchive;

trait UploadableTrait
{

    /**
     * upload file temporary resolver
     * 
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return type
     */
    protected function resolveTempFileName(UploadedFile $file)
    {
        $name      = $file->getClientOriginalName();
        $extension = File::extension($name);
        $subdir    = sha1(time());
        $directory = storage_path() . '/app/uploads/' . $subdir;
        $filename  = sha1(time() . time()) . ".{$extension}";
        return ['directory' => $directory, 'subdir' => $subdir, 'filename' => $filename];
    }

    /**
     * generate view of manifest description
     * 
     * @param array $manifest
     * @return String
     */
    protected function manifestDecorator(array $manifest, $path = 'antares/foundation::modules.manifest')
    {
        return view($path, ['manifest' => $manifest])->render();
    }

    /**
     * unziping module package
     * 
     * @param String $source
     * @param String $destination
     * @return boolean
     * @throws \Exception
     */
    protected function unzip($source, $destination)
    {
        @mkdir($destination, 0777, true);
        $zip = new ZipArchive;
        if ($zip->open($source) === true) {
            $zip->extractTo($destination);
            $zip->close();
            return true;
        } else {
            throw new \Exception('Unable to open module package file');
        }
    }

    /**
     * gets theme name by manifest 
     * 
     * @param String $path
     * @return String
     */
    protected function getPackageName($path)
    {
        if (ends_with($path, '.zip')) {
            $path = dirname($path);
        }
        $manifest = new Manifest(new Filesystem(), $path);
        return $manifest->get('package');
    }

}
