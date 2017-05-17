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

namespace Antares\Support;

use Illuminate\Support\Fluent as SupportFluent;

class Fluent extends SupportFluent
{

    /**
     * Current url
     *
     * @var String
     */
    protected $url = null;

    /**
     * Module controller url
     *
     * @var String
     */
    protected $moduleControllerUrl = null;
    protected $segments            = [];

    /**
     * Create a new fluent container instance.
     *
     * @param  array|object    $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        $this->attributes['active'] = false;
        $request                    = request();
        $this->url                  = $request->url();
        $this->segments             = $request->segments();
    }

    /**
     * check whether element has same request url and link attribute
     * 
     * @return boolean
     */
    public function isActive()
    {
        if (!isset($this->attributes['link'])) {
            return false;
        }
        $childs = isset($this->attributes['childs']) ? $this->attributes['childs'] : [];
        if (empty($childs)) {
            return $this->compare($this->attributes['link']);
        }
        foreach ($childs as $child) {
            if (!isset($child->attributes['link'])) {
                continue;
            }
            if ($this->compare($child->attributes['link'])) {
                return true;
            }
        }
        return $this->compare($this->attributes['link']);
    }

    /**
     * Whether menu item is active
     * 
     * @param String $url
     * @return boolean
     */
    protected function compare($url)
    {

        if ($this->url == $url) {
            return true;
        }
        $segments = $this->segments;
        $count    = count($segments);
        if ($count !== count(array_filter(explode('/', str_replace(url('/'), '', $url))))) {
            return false;
        }

        $segmented = url('/') . '/' . implode('/', array_slice($segments, 0, $count - 1));
        if (count($segments) > 2 && starts_with($url, $segmented)) {
            return true;
        }
        @list($domain, $area, $module, $action) = explode('/', str_replace('http://', '', $url));
        if (!isset($segments[1])) {
            return false;
        }
        return ($segments[1] == $module && isset($segments[2]) && (in_array($segments[2], ['index', 'edit']) or is_numeric($segments[2])));
    }

    /**
     * Whether menu item is active
     * 
     * @param String $url
     * @return boolean
     */
//    protected function compare($url)
//    {
//
//        if ($this->url == $url) {
//            return true;
//        }
//        $segments  = $this->segments;
//        $count     = count($segments);
//        $segmented = url('/') . '/' . implode('/', array_slice($segments, 0, $count - 1));
//        if (count($segments) > 2 && starts_with($url, $segmented)) {
//            return true;
//        }
//        @list($domain, $area, $module, $action) = explode('/', str_replace('http://', '', $url));
//        if (!isset($segments[1])) {
//            return false;
//        }
//        return ($segments[1] == $module && isset($segments[2]) && (in_array($segments[2], ['index', 'edit']) or is_numeric($segments[2])));
//    }

    /**
     * Whether child is active
     * 
     * @return boolean
     */
    public function isChildActive()
    {
        $url = array_get($this->attributes, 'link', '');
        return $this->url == $url;
    }

    /**
     * Whether first child is active
     * 
     * @return boolean
     */
    public function isFirstChildActive()
    {
        $url = array_get($this->attributes, 'link', '');
        @list($domain, $area, $module, $action) = explode('/', str_replace('http://', '', $url));
        if ($this->url == $url) {
            return true;
        }
        $segments = $this->segments;
        if (!isset($segments[1])) {
            return false;
        }
        return ($segments[1] == $module && isset($segments[2]) && (in_array($segments[2], ['index', 'edit', 'mail']) or is_numeric($segments[2])));
    }

}
