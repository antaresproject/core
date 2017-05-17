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

namespace Antares\Foundation\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Event;
use Illuminate\Contracts\Container\Container;

abstract class MenuHandler
{

    /**
     * The foundation implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'       => null,
        'position' => '*',
        'title'    => null,
        'link'     => '#',
        'icon'     => null,
        'type'     => null
    ];

    /**
     * Menu instance.
     *
     * @var \Antares\UI\TemplateBase\Menu
     */
    protected $handler;

    /**
     * Construct a new handler.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     */
    public function __construct(Container $container = null, $name = null)
    {
        if (is_null($container)) {
            $container = app();
        }
        $this->handler   = (!is_null($name)) ? $container->make($name) : $container->make('antares.platform.menu');
        $this->container = $container;
    }

    /**
     * Create a handler.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->getAttribute('id');
        Event::fire('antares.ready: menu.before.' . $id);
        if (!$this->passesAuthorization()) {
            return;
        }
        $args = func_get_args();
        if (isset($args[0])) {
            $menu = $args[0];
            $menu->add($this->getAttribute('id'))
                    ->link($this->getAttribute('link'))
                    ->title($this->getAttribute('title'))
                    ->icon($this->getAttribute('icon'))
                    ->type($this->getAttribute('type'))
                    ->active($this->getAttribute('active'));
        } else {
            $menu = $this->createMenu();
            if ($menu) {
                $this->attachIcon($menu);
            }
        }
        Event::fire('antares.ready: menu.after.' . $id);
    }

    /**
     * Get title attribute
     *
     * @return String
     */
    public function getActiveAttribute()
    {
        return;
    }

    /**
     *  Handle get attributes.
     *
     * @param  string  $name
     *
     * @return mixed
     */
    public function getAttribute($name)
    {
        $method = 'get' . ucfirst($name) . 'Attribute';
        $value  = Arr::get($this->menu, $name);

        if (method_exists($this, $method)) {
            return $this->container->call([$this, $method], ['value' => $value]);
        }

        return $value;
    }

    /**
     * Get the URL.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getLinkAttribute($value)
    {
        return $this->container->make('antares.app')->handles($value);
    }

    /**
     * Create a new menu.
     *
     * @return \Illuminate\Support\Fluent|null
     */
    protected function createMenu()
    {
        $handler = $this->handler->add($this->getAttribute('id'), $this->getAttribute('position'));

        if (!is_null($handler)) {
            $handler->title($this->getAttribute('title'))
                    ->link($this->getAttribute('link'))
                    ->icon($this->getAttribute('icon'))
                    ->entity($this->getAttribute('entity'))
                    ->active($this->getAttribute('active'));
            if (!is_null($type = $this->getAttribute('type'))) {
                $handler->type($type);
            }
            return $handler;
        }
        return false;
    }

    /**
     * Attach icon to menu.
     *
     * @param  \Illuminate\Support\Fluent|null  $menu
     *
     * @return void
     */
    protected function attachIcon(Fluent $menu = null)
    {
        if (!is_null($menu) && !is_null($icon = $this->getAttribute('icon'))) {
            $menu->icon($icon);
        }
    }

    /**
     * Determine if the request passes the authorization check.
     *
     * @return bool
     */
    protected function passesAuthorization()
    {
        if (method_exists($this, 'authorize')) {
            return $this->container->call([$this, 'authorize']);
        }

        return false;
    }

    /**
     *  Handle dynamic calls to the container to get attributes.
     *
     * @param  string  $name
     * @param  array   $parameters
     *
     * @return mixed
     */
    public function __call($name, $parameters)
    {
        return $this->getAttribute($name);
    }

}
