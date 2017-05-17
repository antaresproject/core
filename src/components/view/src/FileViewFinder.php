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

namespace Antares\View;

use Illuminate\View\FileViewFinder as SupportFileViewFinder;

class FileViewFinder extends SupportFileViewFinder implements \Illuminate\View\ViewFinderInterface
{

    /**
     * {@inheritdoc}
     */
    protected function findNamedPathView($name)
    {
        list($namespace, $view) = $this->getNamespaceSegments($name);

        $generatePath = function ($path) use ($namespace) {
            return "{$path}/{$namespace}";
        };
        $paths = array_map($generatePath, $this->paths);
        return $this->findInPaths($view, array_merge($paths, $this->hints[$namespace]));
    }

    /**
     * Set the active view paths.
     *
     * @param  array  $paths
     *
     * @return array
     */
    public function setPaths(array $paths)
    {
        $this->paths = $paths;
    }

}
