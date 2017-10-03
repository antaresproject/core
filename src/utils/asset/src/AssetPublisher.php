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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Asset;

use Antares\Extension\Manager;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class AssetPublisher
{

    /**
     * asset factory instance
     *
     * @var Factory
     */
    protected $assetFactory;

    /**
     * symlinker instance
     *
     * @var AssetSymlinker 
     */
    protected $symlinker;

    /**
     * Extensions Manager instance
     *
     * @var Manager
     */
    protected $extensionsManager;

    /**
     * default scripts position
     *
     * @var String 
     */
    protected $position = null;

    /**
     * extension name
     *
     * @var String 
     */
    protected $extension = '';

    /**
     * AssetPublisher constructor.
     * @param Factory $assetFactory
     * @param AssetSymlinker $symlinker
     * @param Manager $extensionsManager
     */
    public function __construct(Factory $assetFactory, AssetSymlinker $symlinker, Manager $extensionsManager)
    {
        $this->assetFactory         = $assetFactory;
        $this->symlinker            = $symlinker;
        $this->extensionsManager    = $extensionsManager;
        $this->position             = "antares/foundation::scripts";
    }

    /**
     * Sets position.
     *
     * @param string $position
     * @return AssetPublisher
     */
    public function setPosition(string $position) : self {
        $this->position = $position;

        return $this;
    }

    /**
     * get files to publish
     * 
     * @param array $specified
     * @return array
     */
    protected function getFiles($specified)
    {
        $specified = (array) $specified;

        if (php_sapi_name() === 'cli') {
            return [];
        }

        if ($this->extension === null) {
            return [];
        }

        $path = $this->extensionsManager->getExtensionPathByName($this->extension);

        if (!$path) {
            return [];
        }

        $public     = $path . DIRECTORY_SEPARATOR . 'public';
        $filesystem = app(Filesystem::class);
        if (empty($specified)) {
            return $filesystem->allFiles($public);
        }

        $return = [];

        foreach ($specified as $file) {
            $target = $public . DIRECTORY_SEPARATOR . $file;

            if (!file_exists($target) && file_exists(public_path($file))) {
                $target = public_path($file);
            }

            array_push($return, new SplFileInfo($public . DIRECTORY_SEPARATOR . $file, current(explode('/', $file)), $file));
        }

        return $return;
    }

    /**
     * creates symlink as publish and attaches to asset container
     * 
     * @param array $files
     * @param String $extension
     * @param array $before
     * @return Asset
     */
    public function publishAndPropagate(array $files = array(), $extension = null, $before = [])
    {
        $container              = $this->assetFactory->container($this->position);
        $applicationContainer   = $this->assetFactory->container('antares/foundation::application');

        if (empty($files)) {
            return $container;
        }
        if (!is_null($extension)) {
            $this->extension = $extension;
        }

        foreach ($files as $file) {
            $fileRealPath = $file->getRealPath();

            if ($fileRealPath !== false) {
                $name      = $this->extension . DIRECTORY_SEPARATOR . $file->getRelativePathname();
                $published = $this->symlinker->publish($name, $fileRealPath);

                if (!in_array($file->getExtension(), ['css', 'js']) or $published === false) {
                    continue;
                }
            } else {
                $published = $file->getRelativePathname();
            }

            $basename           = str_replace('\\', '/', $published);
            $sluggedBaseName    = str_slug($file->getBasename());

            if ($file->getExtension() === 'css') {
                $applicationContainer->style($sluggedBaseName, $basename);
            } else {
                $container->add($sluggedBaseName, $basename, [], $before);
            }
        }
        return $container;
    }

    /**
     * publish assets depends on extension name
     * 
     * @param String $extension
     * @param mixed $options
     * @param array $before
     * @return Asset
     */
    public function publish($extension, $options = null, $before = [])
    {
        $this->extension = $extension;

        $params = is_string($options) ? config('antares/' . $extension . '::' . $options) : $options;
        $files  = $this->getFiles($params);


        if (empty($files) and $options = (array) $options) {

            foreach ($options as $option) {
                $realPath = public_path($option);
                if (!file_exists($realPath)) {
                    continue;
                }
                $files[] = new SplFileInfo($realPath, str_replace(public_path(), '', $realPath), last(explode('/', $realPath)));
            }
        }
        return $this->publishAndPropagate($files, null, $before);
    }

}
