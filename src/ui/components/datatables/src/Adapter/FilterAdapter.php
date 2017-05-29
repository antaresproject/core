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


namespace Antares\Datatables\Adapter;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Route;
use Antares\Datatables\Html\Builder;
use Illuminate\Support\Collection;
use Illuminate\Routing\Router;
use Illuminate\Http\Request;
use Exception;

class FilterAdapter
{

    /**
     * filters container
     *
     * @var array
     */
    protected $filters = [];

    /**
     * application instance
     *
     * @var Application
     */
    protected $app;

    /**
     *
     * @var Router
     */
    protected $router;

    /**
     *
     * @var Request
     */
    protected $request;

    /**
     *
     * @var UrlGenerator
     */
    protected $url;

    /**
     * selected filters search
     *
     * @var array 
     */
    public $selected = [];

    /**
     * route instance
     *
     * @var Route
     */
    protected $route;

    /**
     * constructing
     * 
     * @param Application $app
     */
    public function __construct(Application $app, Router $router, Request $request, UrlGenerator $url)
    {
        $this->app     = $app;
        $this->router  = $router;
        $this->request = $request;
        $this->url     = $url;

        if (!$request->hasSession()) {
            return;
        }

        $session     = $request->session();
        $this->route = uri();
        if ($session->has($this->route)) {
            $params = $session->get($this->route);
            foreach ($params as $column => $config) {
                if (class_exists($column)) {
                    $this->selected[] = app($column)->sidebar($config);
                } else {
                    $sidebar          = $this->getDeleteSidebarItem($column, $config);
                    $this->selected[] = $sidebar;
                }
            }
        }
    }

    /**
     * creates removable sidebar filter items
     * 
     * @param String $column
     * @param array $config
     * @param mixed $value
     * @return boolean|string
     */
    public function getDeleteSidebarItem($column, $config, $value = null)
    {
        if (!isset($config['classname']) or ! class_exists($config['classname']) or ! isset($config['values'])) {
            return false;
        }
        $instance = $this->app->make($config['classname']);
        $values   = !is_null($value) ? [$value] : $config['values'];
        if (!is_array($values)) {
            return false;
        }
        $names = [];
        foreach ($values as $key => $value) {
            $names[] = $instance->getPatterned($value);
            $instance->setFormData(new Collection($value));
        }
        $name = implode(', ', array_unique($names));


        return view('datatables-helpers::partials._deleted', compact('rel', 'value', 'column', 'instance', 'config'))->with([
                            'route' => $this->route,
                            'name'  => $name
                        ])
                        ->render();
    }

    public function sidebar($column, $config)
    {
        if (!isset($config['classname']) or ! class_exists($config['classname']) or ! isset($config['values'])) {
            return false;
        }
        $instance = $this->app->make($config['classname']);
        $values   = $config['values'];
        if (!is_array($values)) {
            return false;
        }
        $names = [];
        foreach ($values as $key => $value) {
            $names[] = $instance->getPatterned($value);
            $instance->setFormData(new Collection($value));
        }
        $name = implode(', ', array_unique($names));
        return view('datatables-helpers::partials._deleted', compact('rel', 'value', 'column', 'instance', 'config'))->with([
                            'route' => $this->route,
                            'name'  => $name
                        ])
                        ->render();
    }

    /**
     * filter getter
     * 
     * @return boolean|String
     */
    public function getFilters($view = null)
    {
        if (!$this->filters) {
            return false;
        }
        $this->filters = array_unique($this->filters);

        $renderable = false;
        foreach ($this->filters as $filter) {
            if ($filter->renderable) {
                $renderable = true;
                break;
            }
        }
        if (!$renderable) {
            return false;
        }

        $this->attachScripts();


        return view(($view) ? $view : 'datatables-helpers::filters', [
                    'filters'  => $this->filters,
                    'selected' => $this->selected,
                    'route'    => $this->route,
                    'urls'     => [
                        'store'   => handles('antares/foundation::datatables/filters/store'),
                        'destroy' => handles('antares/foundation::datatables/filters/destroy'),
                        'update'  => handles('antares/foundation::datatables/filters/update'),
                    ],
                    'group'    => 'Groups'])->render();
    }

    /**
     * add filter to datatables 
     * 
     * @param string $classname
     * @return boolean|Builder
     */
    public function add($classname)
    {
        if (is_string($classname) and ! class_exists($classname)) {
            throw new Exception(sprintf('Invalid filter object. Classname not exists %s.', $classname));
        }
        app('antares.asset')->container('antares/foundation::application')->add('webpack_forms_basic', '/webpack/forms_basic.js', ['app_cache']);
        $filter = is_object($classname) ? $classname : $this->app->make($classname);
        array_push($this->filters, $filter);

        return $this;
    }

    /**
     * attaches filter scripts
     */
    protected function attachScripts()
    {
        $scripts   = config('datatables-config.scripts');
        $container = $this->app->make('antares.asset')->container(array_get($scripts, 'position'));
        $resources = array_get($scripts, 'resources');
        foreach ($resources as $name => $resourcePath) {
            $container->add($name, $resourcePath);
        }
    }

    /**
     * filter list getter
     * 
     * @return array
     */
    public function getFiltersList()
    {
        return array_unique($this->filters);
    }

}
