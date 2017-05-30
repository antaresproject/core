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

namespace Antares\Foundation\Support\Factories;

use Illuminate\Contracts\Container\Container;
use Antares\Foundation\Exception\NotInstantiableException;
use Antares\Contracts\Foundation\Factory\MenuFactory as Factory;
use ReflectionClass;

class MenuFactory implements Factory
{

    /**
     * @var Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * @var array
     */
    protected $handlers;

    /**
     * @var String 
     */
    protected $name;
    protected $handled;

    /**
     * @var array 
     */
    private $defaultOptions = [
        'pane'       => 'pane.menu.top',
        'name'       => 'default',
        'attributes' => [],
        'title'      => 'antares/foundation::title.menu.top.default',
        'view'       => 'antares/foundation::widgets.top_menu'
    ];

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param String $name
     * @return \Antares\Foundation\Support\Factories\MenuFactory
     */
    public function with($name)
    {
        $this->name = $name;
        $this->container->instance($name, $this->container->make('antares.widget')->make($name));
        return $this;
    }

    /**
     * @param array|String $handlers
     * @return \Antares\Foundation\Support\Factories\MenuFactory
     * @throws NotInstantiableException
     */
    public function withHandlers($handlers)
    {
        $handlerList = is_string($handlers) ? [$handlers] : $handlers;
        foreach ($handlerList as $handler) {
            $reflectionClass = new ReflectionClass($handler);
            if (!$reflectionClass->isInstantiable()) {
                throw new NotInstantiableException(sprintf('Class name %s is not instantiable', $handler));
            }
            $this->handlers[] = $handler;
        }
        return $this;
    }

    /**
     * resolving extension name by realpath
     * @param String $path
     * @return boolean
     * @throws \Exception
     */
    protected function resolveExtensionName($path)
    {
        preg_match("/src(.*?)src/", dirname($path), $match);
        if (!isset($match[1])) {
            throw new \Exception('Unable to resolve valid module path');
        }
        $name = trim($match[1], DIRECTORY_SEPARATOR);
        if (!starts_with($name, ['components', 'modules'])) {
            return false;
        }
        return str_replace('\\', '/', $name);
    }

    /**
     * @param array | String $actions
     * @param array $options
     * @return \Antares\Foundation\Support\Factories\MenuFactory
     */
    public function compose($actions, array $options = null)
    {
        $this->container->make('view')->composer($actions, function() use($options) {
            $this->handleMenus($options);
            $resultView = array_get($options, 'view', $this->defaultOptions['view']);
            if (!view()->exists($resultView)) {
                return false;
            }
            $view = view($resultView, ['container' => $this->name]);
            app('antares.widget')->make(array_get($options, 'pane', $this->defaultOptions['pane']))
                    ->add(array_get($options, 'name', $this->defaultOptions['name']))
                    ->attributes(array_get($options, 'attributes', $this->defaultOptions['attributes']))
                    ->title(trans(array_get($options, 'title', $this->defaultOptions['title'])))
                    ->content($view);
        });
        return $this;
    }

    /**
     * handle menus
     * 
     * @param array $options
     * @return void
     */
    protected function handleMenus($options)
    {
        if (isset($this->handled[$this->name])) {
            return;
        }
        $handlers = array_get($options, 'handlers', $this->handlers);
        if (empty($handlers)) {
            return;
        }
        foreach ($handlers as $handler) {
            if (!class_exists($handler)) {
                continue;
            }
            $element                    = new $handler(app(), $this->name);
            $element->handle();
            $this->handled[$this->name] = $element;
        }
        return true;
    }

}
