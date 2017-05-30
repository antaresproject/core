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


namespace Antares\Foundation\Validation;

use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\File;
use ZipArchive;

class CustomModule extends Validator
{

    /**
     * property contains path to extracted module file
     * 
     * @var String
     */
    protected $manifest = null;

    /**
     * validates correctness of zip module package
     * 
     * @param string $attribute
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $value
     * @param array $parameters
     * @return boolean
     */
    public function validateSource($attribute, $value, $parameters)
    {
        $name      = $value->getClientOriginalName();
        $directory = $value->directory;
        $filename  = $value->filename;
        $extension = File::extension($filename);
        $module    = str_replace('.' . $extension, '', $name);

        if ($value->move($directory, $filename)) {
            $za   = new ZipArchive();
            $za->open($directory . DIRECTORY_SEPARATOR . $filename);
            $list = [];
            for ($i = 0; $i < $za->numFiles; $i++) {
                $stat     = $za->statIndex($i);
                $basename = basename($stat['name']);
                if ($basename == 'manifest.json' && $za->extractTo($directory, array($za->getNameIndex($i)))) { {
                        $this->manifest = $directory . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $basename;
                    }
                }
                $list[] = basename($basename);
            }

            $za->close();
        }

        $failure = false;
        foreach (['products', 'domains', 'fraud', 'addons'] as $category) {
            if (!is_null(antares('memory')->get("extensions.available.{$category}/$module"))) {
                $this->deleteTemporaries($directory, $filename);
                $this->setCustomMessages([$attribute => trans('antares/foundation::install.modules.invalid-category')]);
                $failure = true;
            }
        }

        if (!in_array($module, $list) OR ! in_array('src', $list) OR ! in_array('manifest.json', $list)) {
            $this->deleteTemporaries($directory, $filename);
            $this->setCustomMessages([$attribute => trans('antares/foundation::install.modules.invalid-structure')]);
            $failure = true;
        }

        if (is_null($this->manifest)) {
            $this->deleteTemporaries($directory, $filename);
            $this->setCustomMessages([$attribute => trans('antares/foundation::install.modules.invalid-manifest')]);
            $failure = true;
        } else {
            $finder  = app('antares.extension')->finder();
            $content = $finder->getManifestContents($this->manifest);
            if (!$finder->validateExtensionName($content['name'])) {
                $this->setCustomMessages([$attribute => trans('antares/foundation::install.modules.invalid-name')]);
                $failure = true;
            }
            $this->manifest = $content;
        }


        if ($failure) {
            $this->addFailure($attribute, $attribute, []);
            return false;
        }


        return !$failure;
    }

    /**
     * deletes temporary uploaded files
     * @param String $directory
     * @param String $filename
     * @return boolean
     */
    private function deleteTemporaries($directory, $filename)
    {
        unlink($directory . DIRECTORY_SEPARATOR . $filename);
        rmdir($directory);
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
     * resolved manifest getter
     * 
     * @return array
     */
    public function getManifest()
    {
        return $this->manifest;
    }

}
