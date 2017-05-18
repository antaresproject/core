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

namespace Antares\UI\UIComponents\Twig;

use Antares\UI\UIComponents\Registry\Registry as UIComponentsRegistry;
use Antares\UI\UIComponents\Contracts\GridStack;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Antares\Registry\Registry;
use Twig_SimpleFunction;
use Twig_Extension;
use Exception;

class Component extends Twig_Extension
{

    /**
     * Gridstack adapter instance
     *
     * @var GridStack 
     */
    protected $gridStackAdapter;

    /**
     * Constrcut
     * 
     * @param GridStack $gridStackAdapter
     */
    public function __construct(GridStack $gridStackAdapter)
    {
        $this->gridStackAdapter = $gridStackAdapter;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_UI_UIComponents_Extension_Component';
    }

    /**
     * Creates ui component view helper
     * 
     * @return Twig_SimpleFunction
     */
    protected function component()
    {

        $function = function ($params) {
            if (($classname = $this->validate($params)) === false) {
                return '';
            }
            if (($object = $this->getInstance($classname, $params)) === false) {
                return '';
            }
            $ajax = $this->isAjax();
            $args = func_get_args();
            if ((int) $object->getAttribute('disabled') && !$ajax && !isset($args[1])) {
                return '';
            }

            if (isset($args[1]) && $args[1] == true) {
                return $object->setView('antares/ui-components::admin.partials._base');
            }
            return ($ajax) ? $object->render() : $object->show();
        };
        return new Twig_SimpleFunction(
                'component', $function
        );
    }

    /**
     * Gets ui component instance from registry
     * 
     * @param String $classname
     * @param array $params
     * @return boolean
     */
    protected function getInstance($classname, $params)
    {
        $components = UIComponentsRegistry::get('ui-components');
        if (is_null($components)) {
            return false;
        }
        $object = $components->first(function ($value, $key) use($classname) {
            return $value instanceof $classname;
        });
        if (!$object) {
            return false;
        }

        $attributes       = array_only(array_get($params, 'data'), ['x', 'y', 'width', 'height', 'disabled', 'id']);
        $attributes['id'] = isset($params['id']) ? $params['id'] : null;

        $object->setAttributes($attributes);
        return $object;
    }

    /**
     * Validates ui component before render
     * 
     * @param array $params
     * @return boolean
     */
    protected function validate($params)
    {
        if (array_get($params, 'data.dispatchable') === false) {
            return false;
        }
        if (is_null($classname = array_get($params, 'data.classname'))) {
            return false;
        }
        try {
            if (!class_exists($classname)) {
                return false;
            }
        } catch (Exception $ex) {
            return false;
        }

        return $classname;
    }

    /**
     * Is ajax request
     * 
     * @return boolean
     */
    protected function isAjax()
    {
        return app('request')->ajax();
    }

    /**
     * Gets current uri content dimension parameters
     * 
     * @return type
     */
    private function getCurrentContentDimension()
    {
        $current = Route::current();
        if (is_null($current)) {
            return [];
        }
        $uri        = uri();
        $dimensions = app('config')->get("antares/ui-components::dimensions");
        return (isset($dimensions[$uri])) ? $dimensions[$uri] : [];
    }

    /**
     * Gets core content params as ui component
     * 
     * @return Twig_SimpleFunction
     */
    protected function contentParams()
    {

        $function = function ($name = 'ui-components') {
            try {
                $dimension  = $this->getCurrentContentDimension();
                $repository = app('ui-components');
                $config     = app('config')->get('antares/ui-components::content');
                $attributes = $repository->findOne($config['attributes'], uri());
                if (!isset($attributes['width']) && !isset($attributes['height'])) {
                    $attributes = array_merge($attributes, $dimension);
                }
                $defaultHeight        = array_get($attributes, 'default_height', array_get($config, 'attributes.default_height'));
                $defaultWidth         = array_get($attributes, 'default_width', array_get($config, 'attributes.default_width'));
                $attributes['height'] = !isset($attributes['height']) ? $defaultHeight : $attributes['height'];
                $attributes['width']  = !isset($attributes['width']) ? $defaultWidth : $attributes['width'];
                return array_merge(array_get($config, 'attributes'), $attributes);
            } catch (Exception $e) {
                Log::emergency($e);
                return false;
            }
        };

        return new Twig_SimpleFunction(
                'content_params', $function
        );
    }

    /**
     * Creates twig view helper to verify whether page use widgetable layout
     * 
     * @return Twig_SimpleFunction
     */
    protected function componentAttributes()
    {
        $function = function ($widget) {
            $classname = array_get($widget['attributes'], 'attributes.classname');
            if (is_null($classname)) {
                return '';
            }
            $instance = null;
            $widgets  = Registry::get('ui-components');
            if (empty($widgets)) {
                return '';
            }
            foreach ($widgets as $widget) {
                if (get_class($widget) == $classname) {
                    $instance = $widget;
                    break;
                }
            }
            if (is_null($instance)) {
                return '';
            }
            return serialize($instance->getShared());
        };
        return new Twig_SimpleFunction(
                'component_attributes', $function
        );
    }

    /**
     * Shows forced ui component
     * 
     * @return Twig_SimpleFunction
     */
    public function componentForced()
    {
        $function = function ($name = null, array $params = []) {

            if (is_null($name)) {
                return '';
            }
            $components = UIComponentsRegistry::get('ui-components', []);
            $component  = null;
            foreach ($components as $item) {
                if ($item->getSnakedName() === $name) {
                    $component = $item;
                    break;
                }
            }
            if (is_null($component)) {
                return '';
            }
            $this->gridStackAdapter->scripts();
            if (!empty($params)) {
                $component->setAttributes($params);
            }
            $component->setView('antares/ui-components::admin.partials._forced');
            app('antares.asset')->container('antares/foundation::application')->add('webpack_forms_basic', '/webpack/forms_basic.js', ['app_cache'])->add('webpack_gridstack', '/webpack/view_gridstack.js', ['app_cache']);
            return $component;
        };

        return new Twig_SimpleFunction(
                'component_forced', $function
        );
    }

    /**
     * Prepares ui components list
     * 
     * @return Twig_SimpleFunction
     */
    public function componentsList()
    {
        $function = function () {

            $widgets = app('antares.widget')->make('menu.top.right')->items();

            if (!isset($widgets["ui-components-selector"])) {
                return [];
            }
            $childs = $widgets["ui-components-selector"]->childs;
            if (empty($childs)) {
                return [];
            }
            $disabled = Registry::get('ui-components.disabled');
            $viewed   = Registry::get('ui-components.viewed');

            foreach ($childs as $index => $child) {
                $classname = array_get($child->attributes, 'classname');
                if (!is_null($viewed) and ! $viewed->contains($classname)) {
                    unset($childs[$index]);
                }
                if (!is_null($disabled) and $disabled->contains($classname)) {
                    unset($childs[$index]);
                }
            }
            return $childs;
        };
        return new Twig_SimpleFunction(
                'components_list', $function
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            $this->component(), $this->contentParams(), $this->componentAttributes(), $this->componentForced(), $this->componentsList()
        ];
    }

}
