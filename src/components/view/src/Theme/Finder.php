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


namespace Antares\View\Theme;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Container\Container;
use Antares\Contracts\Theme\Finder as FinderContract;

class Finder implements FinderContract
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Construct a new finder.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Detect available themes.
     *
     * @return \Illuminate\Support\Collection
     *
     * @throws \RuntimeException
     */
    public function detect()
    {
        $themes = new Collection();
        $file   = $this->app->make('files');
        $path   = rtrim($this->app->make('path.public'), '/') . '/themes/';

        $folders = $file->directories($path);

        foreach ($folders as $folder) {
            $name          = $this->parseThemeNameFromPath($folder);
            $themes[$name] = new Manifest($file, rtrim($folder, '/') . '/');
        }

        return $themes;
    }

    /**
     * Get folder name from full path.
     *
     * @param  string   $path
     *
     * @return string
     */
    protected function parseThemeNameFromPath($path)
    {
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
        $path = explode(DIRECTORY_SEPARATOR, $path);

        return array_pop($path);
    }

}
