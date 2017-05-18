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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents;

use Antares\UI\UIComponents\TemplateManifest as Manifest;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;

class TemplateFinder
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Templates configuration
     * 
     * @var array
     */
    protected $config;

    /**
     * Construct
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     */
    public function __construct(Container $app)
    {
        $this->app    = $app;
        $this->config = array_merge($app->make('config')->get('antares/ui-components::templates', []), ['public' => $app->make('path.public')]);
    }

    /**
     * Detect available themes.
     *
     * @return \Illuminate\Support\Collection
     * @throws \RuntimeException
     */
    public function detect()
    {
        $themes  = new Collection();
        $file    = $this->app->make('files');
        $path    = rtrim(__DIR__ . '/../' . $this->config['indexes_path']);
        $folders = $file->directories($path);
        foreach ($folders as $folder) {
            $templateDir = class_basename($folder);
            $name        = $this->parseThemeNameFromPath($folder);
            $manifest    = new Manifest($file, $this->config + ['dir' => $templateDir], rtrim($folder, '/') . '/');
            $items       = $manifest->items();
            if (is_null($items)) {
                continue;
            }
            $themes[$name] = $items->getAttributes();
        }
        return $themes;
    }

    /**
     * Gets folder name from full path.
     *
     * @param  String   $path
     * @return String
     */
    protected function parseThemeNameFromPath($path)
    {
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
        $path = explode(DIRECTORY_SEPARATOR, $path);

        return array_pop($path);
    }

}
