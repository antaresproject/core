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

class Asset
{

    /**
     * Asset Dispatcher instance.
     *
     * @var \Antares\Asset\Dispatcher
     */
    protected $dispatcher;

    /**
     * The asset container name.
     *
     * @var string
     */
    protected $name;

    /**
     * The asset container path prefix.
     *
     * @var string
     */
    protected $path = null;

    /**
     * All of the registered assets.
     *
     * @var array
     */
    protected $assets = [];

    /**
     * Create a new asset container instance.
     *
     * @param  string  $name
     * @param  \Antares\Asset\Dispatcher  $dispatcher
     */
    public function __construct($name, Dispatcher $dispatcher)
    {
        $this->name       = $name;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Enable asset versioning.
     *
     * @return $this
     */
    public function addVersioning()
    {
        $this->dispatcher->addVersioning();

        return $this;
    }

    /**
     * Disable asset versioning.
     *
     * @return $this
     */
    public function removeVersioning()
    {
        $this->dispatcher->removeVersioning();

        return $this;
    }

    /**
     * Set the asset container path prefix.
     *
     * @param  string|null  $path
     *
     * @return $this
     */
    public function prefix($path = null)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Add an asset to the container.
     *
     * The extension of the asset source will be used to determine the type
     * of asset being registered (CSS or JavaScript). When using a non-standard
     * extension, the style/script methods may be used to register assets.
     *
     * <code>
     *     // Add an asset to the container
     *     Antares\Asset::container()->add('jquery', 'js/jquery.js');
     *
     *     // Add an asset that has dependencies on other assets
     *     Antares\Asset::add('jquery', 'js/jquery.js', 'jquery-ui');
     *
     *     // Add an asset that should have attributes applied to its tags
     *     Antares\Asset::add('jquery', 'js/jquery.js', null, array('defer'));
     * </code>
     *
     * @param  string  $name
     * @param  string  $source
     * @param  string|array  $dependencies
     * @param  string|array  $attributes
     * @param  string|array  $replaces
     *
     * @return $this
     */
    public function add($name, $source, $dependencies = [], $attributes = [], $replaces = [])
    {
        if (!is_null($from      = array_get($attributes, 'from')) && !is_null($extension = extensions($from))) {
            $extensions = app('antares.extension')->getAvailableExtensions();
            $path       = null;
            foreach ($extensions as $extension) {
                if (str_contains($extension->getPackageName(), $from)) {
                    $path = $extension->isActivated() ? $extension->getPath() : null;
                    break;
                }
            }


            $realPath = $path . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $source;
            if (file_exists($realPath)) {
                $symlinker  = app(AssetSymlinker::class);
                $symlinker->setPublishPath(public_path('packages/antares'));
                $sourceName = last(explode('/', $source));
                $symlinker->publish($sourceName, $realPath);
                $source     = '/packages/antares/' . $sourceName;
            }
        }
        $type = (pathinfo($source, PATHINFO_EXTENSION) == 'css') ? 'style' : 'script';

        return $this->$type($name, $source, $dependencies, $attributes, $replaces);
    }

    /**
     * Add a CSS file to the registered assets.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  string|array  $dependencies
     * @param  string|array  $attributes
     * @param  string|array  $replaces
     *
     * @return $this
     */
    public function style($name, $source, $dependencies = [], $attributes = [], $replaces = [])
    {
        if (!array_key_exists('media', $attributes)) {
            $attributes['media'] = 'all';
        }

        $this->register('style', $name, $source, $dependencies, $attributes, $replaces);

        return $this;
    }

    /**
     * Add a JavaScript file to the registered assets.
     *
     * @param  string  $name
     * @param  string  $source
     * @param  string|array  $dependencies
     * @param  string|array  $attributes
     * @param  string|array  $replaces
     *
     * @return $this
     */
    public function script($name, $source, $dependencies = [], $attributes = [], $replaces = [])
    {
        $this->register('script', $name, $source, $dependencies, $attributes, $replaces);

        return $this;
    }

    /**
     * Add a JavaScript code to the registered assets.
     * 
     * @param  string  $name
     * @param  string  $source
     * @param  string|array  $dependencies
     * @param  string|array  $attributes
     * @param  string|array  $replaces
     */
    public function inlineScript($name, $source = null, $dependencies = [], $attributes = [], $replaces = [])
    {
        $this->register('inline', $name, $source, $dependencies, $attributes, $replaces);
    }

    /**
     * Add an asset to the array of registered assets.
     *
     * @param  string  $type
     * @param  string|array  $name
     * @param  string  $source
     * @param  string|array  $dependencies
     * @param  string|array  $attributes
     * @param  string|array  $replaces
     *
     * @return void
     */
    protected function register($type, $name, $source, $dependencies, $attributes, $replaces)
    {
        $dependencies = (array) $dependencies;
        $attributes   = (array) $attributes;
        $replaces     = (array) $replaces;

        if (is_array($name)) {
            $replaces = array_merge($name, $replaces);
            $name     = '*';
        }

        $this->assets[$type][$name] = [
            'source'       => $source,
            'dependencies' => $dependencies,
            'attributes'   => $attributes,
            'replaces'     => $replaces,
        ];
    }

    /**
     * Get the links to all of the registered CSS assets.
     *
     * @return string
     */
    public function styles()
    {
        return $this->group('style');
    }

    /**
     * Get the links to all of the registered JavaScript assets.
     *
     * @return string
     */
    public function scripts()
    {
        return $this->group('script') . $this->group('inline');
    }

    /**
     * Get inline code to all of the registered JavaScript scripts.
     *
     * @return string
     */
    public function inline()
    {
        return $this->group('inline');
    }

    /**
     * Get the links to all the registered CSS and JavaScript assets.
     *
     * @return string
     */
    public function show()
    {
        return $this->group('script') . $this->group('style') . $this->group('inline');
    }

    /**
     * Get all of the registered assets for a given type / group.
     *
     * @param  string  $group
     *
     * @return string
     */
    protected function group($group)
    {
        return $this->dispatcher->run($group, $this->assets, $this->path);
    }

    /**
     * dispatcher instance getter
     * 
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function webpack()
    {

        $scripts = array_merge(['/packages/core/js/on-load.js', '/packages/core/js/datatable-helpers.js'], $this->dispatcher->scripts('script', $this->assets, $this->path));

        $internals = [];
        $externals = [];

        foreach ($scripts as $script) {
            if (starts_with($script, '//')) {
                $externals[] = $script;
                continue;
            }
            if (!file_exists(public_path($script))) {
                continue;
            }
            $internals[] = file_get_contents(public_path($script));
        }

        $return = [];
        foreach ($externals as $external) {
            $return[] = '<script  src="' . $external . '" ></script>';
        }

        $filename = 'packages/' . str_replace(['/', '}', '{', '?'], '_', uri()) . '.js';
        $path     = sandbox_path($filename);

        $input = implode(PHP_EOL, array_merge($internals, $this->dispatcher->scripts('inline', $this->assets, $this->path)));

        if (env('APP_ENV') === 'production') {
            $input = new JSMin($input);
        }

        file_put_contents($path, $input);
        $return[] = '<script  src="' . asset($filename) . '?t=' . time() . '" ></script>';



        return implode(PHP_EOL, $return);
    }

    /**
     * applying RequireJs for scripts
     * 
     * @return String
     */
    public function requireJs()
    {
        $scripts = $this->dispatcher->scripts('script', $this->assets, $this->path);

        $required = [];
        $string   = '';
        if (isset($this->assets['script'])) {
            foreach ($this->assets['script'] as $js) {
                if (!is_null($require = array_get($js, 'attributes.require'))) {
                    $required["'" . implode("','", $require) . "'"][] = $js['source'];
                }
            }


            $idx = 0;
            foreach ($required as $names => $scripts) {
                $require = "require([$names],function(){ " . "require(['" . implode("','", $scripts) . "'],function(){ :replace })" . "  });";
                if ($idx > 0) {
                    $string = str_replace(':replace', $require, $string);
                } else {
                    $string = $require;
                }
                ++$idx;
            }
        }

        $inlines = implode('', $this->dispatcher->scripts('inline', $this->assets, $this->path));

        $string = !strlen($string) ? $inlines : str_replace(':replace', $inlines . $this->afterLoad(), $string);


        $require = "['" . implode("','", $scripts) . "']";
        return $this->requireInline($require, $string);
    }

    /**
     * generate gridstack inline scripts
     * 
     * @return String
     */
    protected function afterLoad()
    {

        $inline = <<<EOD
            $(".grid-stack").css("opacity", "1");
            $(document).ready(function () {
                if($('.activity-logger-filter').length){
                    $('.activity-logger-filter').select2();
                }
                componentHandler.upgradeAllRegistered();
                $('.card > * > *, .tbl-c > *,form').css('opacity', '1'); 
                AntaresForms.elements.tooltip();
            });
     
           
EOD;
        return $inline;
    }

    /**
     * generate gridstack inline scripts
     * 
     * @return String
     */
    protected function requireInline($scripts, $afterLoad = null)
    {
        $config  = config('require_js');
        $main    = array_get($config, 'main', "");
        $default = array_get($config, 'default', []);
        $child   = array_get($default, 'childs', []);
        if (isset($default['childs'])) {
            unset($default['childs']);
        }

        $defaults = "'" . implode("','", $default) . "'";
        $childs   = "'" . implode("','", $child) . "'";


        $config = $this->requireJsCache();
        $inline = <<<EOD
            $config
            require(['$main'], function () {

                require(["jquery", "jquery-ui","moment", "jquery-ui-daterangepicker","datetimepicker", "qtip"], function ($) {
                
                    require([$defaults], function () {
                        require([$childs], function () {
                            $afterLoad
                        });
                    });
                });
          });      
           
EOD;
        return $inline;
    }

    /**
     * Whether require js should be cached
     * 
     * @return String
     */
    protected function requireJsCache()
    {
        if (!config('require_js.cache')) {
            return <<<EOD
            require.config({
                urlArgs: "antares=" + (new Date()).getTime()
            });           
EOD;
        }
        return '';
    }

}
