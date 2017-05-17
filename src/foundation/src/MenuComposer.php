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

namespace Antares\Foundation;

use Antares\Registry\Registry;

final class MenuComposer
{

    /**
     * singleton instance
     *
     * @var object
     * @access private
     */
    private static $oInstance = false;

    /**
     * application instance
     *
     * @var Application
     */
    private $app;

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
     * singleton implementation
     *
     * @return Singleton
     * @static
     */
    public static function getInstance()
    {
        if (self::$oInstance == false) {
            self::$oInstance = new self();
        }
        return self::$oInstance;
    }

    /**
     * constructing
     */
    private function __construct()
    {
        $this->app = app();
    }

    /**
     * listening "on compose" event and attaches menu
     * 
     * @param String $classname
     * @return boolean
     */
    public function compose($classname)
    {
        if (!class_exists($classname)) {
            return false;
        }
        $instance = $this->app->make($classname);
        $params   = $instance->getAttribute('boot');
        $group    = array_get($params, 'group');
        $this->app->instance($group, app('antares.widget')->make($group));

        app('view')->composer(array_get($params, 'on'), function() use($instance, $group, $params) {
            if (!Registry::isRegistered('menu.' . $group)) {
                $instance->__construct($this->app, $group);
                $instance->handle();
                Registry::set('menu.' . $group, $instance);
            }
            $this->content($params);
        });
    }

    /**
     * creates content of menu widget
     * 
     * @param array $params
     * @return boolean
     */
    protected function content(array $params = [])
    {
        $resultView = array_get($params, 'view', $this->defaultOptions['view']);
        if (!view()->exists($resultView)) {
            return false;
        }
        return $this->app->make('antares.widget')
                        ->make(array_get($params, 'pane', $this->defaultOptions['pane']))
                        ->add(array_get($params, 'name', $this->defaultOptions['name']))
                        ->attributes(array_get($params, 'attributes', $this->defaultOptions['attributes']))
                        ->title(array_get($params, 'title', $this->defaultOptions['title']))
                        ->content(view($resultView, ['container' => array_get($params, 'group')]));
    }

}
