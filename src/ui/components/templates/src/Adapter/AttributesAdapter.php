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

namespace Antares\UI\UIComponents\Adapter;

class AttributesAdapter
{

    /**
     * instance of template configuration
     * 
     * @var array
     */
    protected $attributes = null;

    /**
     * instance of template configuration
     * 
     * @var array
     */
    protected $params = null;

    /**
     * instance of template configuration
     * 
     * @var array
     */
    protected $config = null;

    /**
     * widget name
     * 
     * @var String 
     */
    private $name;

    public function __construct($name, $params = null)
    {
        $this->name   = $name;
        $this->config = app('config')->get('antares/ui-components::defaults.attributes');
        $this->params = $params;
    }

    /**
     * created defaults dimenstion settings
     * 
     * @param array $config
     */
    public function defaults()
    {
        $defaults = $this->config;
        if (!isset($defaults['width'])) {
            $defaults['width'] = $defaults['default_width'];
        }
        if (!isset($defaults['height'])) {
            $defaults['height'] = $defaults['default_height'];
        }
        return $defaults;
    }

    /**
     * widget options
     * 
     * @return array
     */
    public function options()
    {
        $params = $this->params;
        return [
            'id'      => isset($params['id']) ? $params['id'] : null,
            'data'    => isset($params['data']) ? $params['data'] : null,
            'widgets' => isset($params['widgets']) ? $params['widgets'] : null,
        ];
    }

    /**
     * widget attributes manager
     * 
     * @return array
     */
    public function attributes(array $current = array())
    {
        $attributes               = isset($this->params['attributes']) ? $this->params['attributes'] : [];
        $this->attributes         = array_merge(array_merge($this->config, $attributes), $current);
        $this->attributes['name'] = $this->name;
        $this->attributes['x']    = isset($attributes['x']) ? $attributes['x'] : $this->attributes['x'];
        $this->attributes['y']    = isset($attributes['y']) ? $attributes['y'] : $this->attributes['y'];

        $width  = isset($this->attributes['width']) ? $this->attributes['width'] : $this->attributes['default_width'];
        $height = isset($this->attributes['height']) ? $this->attributes['height'] : $this->attributes['default_height'];

        $this->attributes['width']  = isset($attributes['width']) ? $attributes['width'] : $width;
        $this->attributes['height'] = isset($attributes['height']) ? $attributes['height'] : $height;
        return $this->attributes;
    }

}
