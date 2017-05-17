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


namespace Antares\Translation;

use Illuminate\Translation\FileLoader as BaseFileLoader;

class FileLoader extends BaseFileLoader
{

    /**
     * {@inheritdoc}
     */
    protected function loadNamespaceOverrides(array $lines, $locale, $group, $namespace)
    {
        $files = [
            "{$this->path}/packages/{$namespace}/{$locale}/{$group}.php",
            "{$this->path}/vendor/{$locale}/{$namespace}/{$group}.php",
        ];

        foreach ($files as $file) {
            $lines = $this->mergeEnvironments($lines, $file);
        }

        return $lines;
    }

    /**
     * Merge the items in the given file into the items.
     *
     * @param  array   $lines
     * @param  string  $file
     *
     * @return array
     */
    public function mergeEnvironments(array $lines, $file)
    {
        if ($this->files->exists($file)) {
            $lines = array_replace_recursive($lines, $this->files->getRequire($file));
        }

        return $lines;
    }

    /**
     * hints getter
     * 
     * @return array
     */
    public function getHints()
    {
        return $this->hints;
    }

    /**
     * Replace hints after publishing translations
     * 
     * @return void
     */
    public function replaceHints()
    {
        foreach ($this->hints as $namespace => $path) {
            $path = $this->getLangPath($namespace);
            if (!is_dir($path)) {
                continue;
            }
            $this->hints[$namespace] = $path;
        }
        return;
    }

    /**
     * Adds published namespace path to hints container
     * 
     * @param String $namespace
     * @param String $hint
     * @return String
     */
    public function addPublishedNamespace($namespace, $hint)
    {
        $path                    = $this->getLangPath($namespace);
        return (is_dir($path)) ? $this->hints[$namespace] = $path : $this->addNamespace($namespace, $hint);
    }

    /**
     * Gets resource lang path after publish translations
     * 
     * @param String $namespace
     * @return String
     */
    protected function getLangPath($namespace)
    {
        return resource_path(implode(DIRECTORY_SEPARATOR, ['lang', area(), $namespace]));
    }

}
