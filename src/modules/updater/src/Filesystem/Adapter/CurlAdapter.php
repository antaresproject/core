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

use Symfony\Component\HttpFoundation\File\File;
use ZipArchive;

class CurlAdapter
{

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
            $zip->extractTo(dirname($path));
            $zip->close();
            $file   = new File($path);
            $return = $file->getPath() . DIRECTORY_SEPARATOR . str_replace('.' . $file->getExtension(), '', $file->getBasename());
            unlink($path);
            return $return;
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

    /**
     * download migration file from external source
     */
    public function download($url)
    {
        $directory = str_random(40);
        $target    = storage_path("app/updates/{$directory}");
        if (!is_dir($target)) {
            mkdir($target, 0777, true);
        }
        $path = $target . DIRECTORY_SEPARATOR . last(explode('/', $url));
        $fh   = fopen($path, 'w');
        $ch   = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FILE, $fh);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // this will follow redirects
        curl_exec($ch);
        curl_close($ch);
        fclose($fh);
        return $path;
    }

}
