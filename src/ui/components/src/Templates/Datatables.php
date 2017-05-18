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

namespace Antares\UI\UIComponents\Templates;

use Antares\UI\UIComponents\Adapter\AbstractTemplate;
use Illuminate\Support\Facades\Input;
use Antares\Registry\Registry;

class Datatables extends AbstractTemplate
{

    /**
     * Name of template used by ui component
     * 
     * @var String
     */
    protected $template = 'datatables';

    /**
     * Ui component attributes
     * 
     * @var array
     */
    protected $attributes = [
        'titlable' => false,
        'editable' => false,
        'nestable' => false,
    ];

    /**
     * Static attributes collection
     *
     * @var \Illuminate\Support\Collection
     */
    protected static $staticAttributes = [];

    /**
     * {@inherited}
     */
    public function render()
    {
        
    }

    /**
     * Ui component attribute getter
     * 
     * @param String $name
     * @return mixed
     */
    protected static function getWidgetAttribute($name)
    {
        if (!empty(self::$staticAttributes)) {
            return array_get(self::$staticAttributes, $name);
        }
        $classname = get_called_class();
        if (app('request')->ajax()) {
            self::setAjaxAttributes($classname);
        } else {
            $components = Registry::get('ui-components', []);

            foreach ($components as $component) {
                if ($classname !== get_class($component)) {
                    continue;
                }
                self::$staticAttributes = $component->getAttributes();
            }
        }
        return array_get(self::$staticAttributes, $name);
    }

    /**
     * When ajax request with changed ui component dimensions
     * 
     * @param String $classname
     * @return array
     */
    private static function setAjaxAttributes($classname)
    {
        self::$staticAttributes = app($classname)->getAttributes();
        $width                  = Input::get('width');
        if ($width) {
            $width = (int) (12 * $width) / 100;
            array_set(self::$staticAttributes, 'width', $width);
        }
        $height = Input::get('height');
        if ($height) {
            $height = (int) (12 * $height) / 100;
            array_set(self::$staticAttributes, 'height', $height);
        }
        return self::$staticAttributes;
    }

}
