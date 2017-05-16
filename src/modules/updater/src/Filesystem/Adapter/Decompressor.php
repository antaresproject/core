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






namespace Antares\Updater\Filesystem\Adapter;

use Antares\Updater\Contracts\Decompressor as DecompressorContract;
use Symfony\Component\HttpFoundation\File\File;
use ZipArchive;

class Decompressor implements DecompressorContract
{

    public function __construct()
    {
        ;
    }

    /**
     * decompressing migration file
     * 
     * @param String $path
     * @return boolean|string
     * @throws Exception
     */
    public function decompress($path)
    {
        if (!$this->isCompressed($path)) {
            throw new Exception('Migration file has invalid extension. Only *.zip file is correct.', 502);
        }
        $zip = new ZipArchive();
        if ($zip->open($path) === TRUE) {
            $file        = new File($path);
            $extractPath = $file->getPath() . DIRECTORY_SEPARATOR . str_replace('.' . $file->getExtension(), '', $file->getFilename());
            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0777);
            }
            $zip->extractTo($extractPath);
            $zip->close();
            return $extractPath;
        }
        return false;
    }

    /**
     * does the compressed migration file has valid extension
     * 
     * @param String $fileName
     * @return boolean
     */
    private function isCompressed($fileName)
    {
        return pathinfo($fileName, PATHINFO_EXTENSION) === "zip";
    }

}
