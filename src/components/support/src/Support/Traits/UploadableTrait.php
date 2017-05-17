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
 namespace Antares\Support\Traits;

use Antares\Support\Str;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait UploadableTrait
{
    /**
     * Save uploaded file into directory.
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $file
     * @param  string  $path
     *
     * @return string
     */
    protected function saveUploadedFile(UploadedFile $file, $path)
    {
        $file->move($path, $filename = $this->getUploadedFilename($file));

        return $filename;
    }

    /**
     * Delete uploaded from directory.
     *
     * @param  string  $file
     *
     * @return bool
     */
    protected function deleteUploadedFile($file)
    {
        return File::delete($file);
    }

    /**
     * Get uploaded filename.
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $file
     *
     * @return string
     */
    protected function getUploadedFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();

        return sprintf('%s.%s', Str::random(10), $extension);
    }
}
