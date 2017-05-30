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
use Illuminate\Filesystem\Filesystem;
use Antares\View\Theme\Manifest;
use ZipArchive;

trait PackageValidationTrait
{

    /**
     * directory contains uploaded file
     * 
     * @var String
     */
    protected $directory;

    /**
     * hashed name of uploaded file
     * 
     * @var String 
     */
    protected $filename;

    /**
     * extension of uploaded file
     * 
     * @var String 
     */
    protected $extension;

    /**
     * name of file
     *
     * @var String 
     */
    protected $module;

    /**
     * property contains path to extracted module file
     * 
     * @var String
     */
    protected $manifest = null;

    /**
     *  manifest file pattern
     * 
     * @var String 
     */
    protected $manifestPattern = 'manifest.json';

    /**
     * parse uploaded package
     * 
     * @param array | string $attribute
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $value
     * @param array | string $parameters
     */
    protected function parsePackage($attribute, UploadedFile $value, $parameters)
    {
        $name            = $value->getClientOriginalName();
        $this->directory = $directory       = $value->directory;
        $this->filename  = $filename        = $value->filename;
        $this->extension = $extension       = File::extension($filename);
        $this->module    = $module          = str_replace('.' . $extension, '', $name);

        if ($value->move($directory, $filename)) {
            $za   = new ZipArchive();
            $za->open($directory . DIRECTORY_SEPARATOR . $filename);
            $list = [];
            for ($i = 0; $i < $za->numFiles; $i++) {
                $stat     = $za->statIndex($i);
                $basename = basename($stat['name']);
                if ($basename == $this->manifestPattern && $za->extractTo($directory, array($za->getNameIndex($i)))) { {

                        $this->manifest = $directory . DIRECTORY_SEPARATOR . $basename;
                    }
                }
                $list[] = basename($basename);
            }

            $za->close();
        }

        return $list;
    }

    /**
     * deletes temporary uploaded files
     * 
     * @param String $directory
     * @param String $filename
     * @return boolean
     */
    private function deleteTemporaries($directory)
    {
        if (is_dir($directory)) {
            $fileSystem = new Filesystem();
            $fileSystem->deleteDirectory($directory, false);
        }
        $this->manifest = null;
        return true;
    }

    /**
     * get resolved filename path
     * 
     * @return String
     */
    public function getResolvedFilename()
    {
        return $this->filename;
    }

    /**
     * failure validation, add custom messages
     * 
     * @param array | String $attribute
     * @param String $message
     * @return boolean
     */
    protected function failure($attribute, $message = null)
    {
        $this->deleteTemporaries($this->directory);
        $this->setCustomMessages([$attribute => trans($message)]);
        $this->addFailure($attribute, $attribute, []);
        return false;
    }

    /**
     * theme manifest content resolver
     * 
     * @return Manifest
     */
    protected function resolveThemeManifestContent()
    {
        return new Manifest(new Filesystem(), dirname($this->manifest));
    }

}
