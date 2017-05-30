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

use Antares\UI\UIComponents\Traits\ComponentTrait;
use Antares\UI\UIComponents\Contracts\UiComponent;
use Antares\UI\UIComponents\Service\Service;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class AbstractTemplate implements UiComponent
{

    use ComponentTrait;

    /**
     * Name of template used by widget
     * 
     * @var String
     */
    protected $template = 'default';

    /**
     * name of widget
     * 
     * @var String
     */
    public $name = '';

    /**
     * widget description 
     * 
     * @var String 
     */
    public $description = '';

    /**
     * optional rules configuration
     * 
     * @var array
     */
    protected $rules;

    /**
     * default attributes
     * @var array
     */
    protected $attributes = [];

    /**
     * @var UiComponents
     */
    protected $model;

    /**
     * widget user defined params
     * 
     * @var array 
     */
    protected $params;

    /**
     * @var numeric
     */
    protected $id = null;

    /**
     * @var array
     */
    private $widgets = [];

    /**
     * @var String
     */
    public $view = 'antares/ui-components::admin.partials._childable';

    /**
     *
     * @var type 
     */
    protected $templateAdapter = null;

    /**
     *
     * @var type 
     */
    protected $attributesAdapter = null;

    /**
     * whether render only widget content with template
     *
     * @var boolean 
     */
    protected $renderOnlyWithTemplate = false;

    /**
     * List of views where widget should be disabled
     *
     * @var array 
     */
    protected $disabled = [];

    /**
     * Where widget should be viewed 
     *
     * @var array
     */
    protected $views = ['*'];

    /**
     * Optional title of widget
     *
     * @var String
     */
    protected $title = '';

    /**
     * construct widget implementation & fill default attributes
     */
    public function __construct()
    {
        $this->setDefaults();
        $this->id              = array_get($this->attributes, 'id');
        $this->templateAdapter = new TemplateAdapter($this->template);
    }

    /**
     * set default widget attributes
     * 
     * @return array
     */
    protected function setDefaults()
    {
        $this->attributes['name']      = $this->name;
        $this->attributes['classname'] = get_called_class();
        $attributesAdapter             = new AttributesAdapter($this->name);
        $defaults                      = $attributesAdapter->defaults();
        if (!isset($this->attributes['width']) && isset($this->attributes['default_width'])) {
            $this->attributes['width'] = $this->attributes['default_width'];
        }
        if (!isset($this->attributes['height']) && isset($this->attributes['default_height'])) {
            $this->attributes['height'] = $this->attributes['default_height'];
        }
        $this->attributes = array_merge($defaults, $this->attributes);

        $attributes = app(Service::class)->findOne(array_merge($defaults, $this->attributes), uri());

        return $this->attributes = array_merge($this->attributes, $attributes);
    }

    /**
     * runs child widget before render
     */
    public function init()
    {
        
    }

    /**
     * render widget
     */
    abstract public function render();

    /**
     * render widget
     */
    public function __toString()
    {
        try {
            $params = $this->fill();
            return View::make($this->view)->with($params)->render();
        } catch (Exception $e) {
            Log::emergency($e);
            return $e->getMessage();
        }
    }

    /**
     * alternative for __toString method
     * 
     * @return String
     */
    public function show()
    {
        return $this->__toString();
    }

    /**
     * fill widget params
     * 
     * @return array
     */
    public function fill()
    {
        $params            = array_merge($this->attributes, [
            'id'            => $this->id,
            'widget'        => $this->model,
            'name'          => $this->name,
            'title'         => strlen($this->title) > 0 ? $this->title : $this->name,
            'description'   => $this->description,
            'editable'      => $this->editable(),
            'widgets'       => $this->widgets,
            'template'      => $this->template,
            'only_template' => $this->renderOnlyWithTemplate
        ]);
        $params['content'] = $this->templateAdapter->share($params)->decorate($this->render());
        return $params;
    }

    /**
     * getting all controls available in edition form of widget
     * 
     * @return array
     */
    public function controls()
    {
        $this->form();
        $form      = app('antares.form')->of("antares.ui-components: custom");
        $fieldsets = $form->grid->fieldsets();
        $controls  = [];
        foreach ($fieldsets as $fieldset) {
            foreach ($fieldset->controls as $control) {
                array_push($controls, $control->name);
            }
        }
        return $controls;
    }

    /**
     * optional widget routes
     * 
     * @return boolean
     */
    public static function routes()
    {
        return false;
    }

    /**
     * gets shared widget attributes
     * 
     * @return array
     */
    public function getShared()
    {
        return [];
    }

    /**
     * Attributes setter
     * 
     * @param array $attributes
     * @return \Antares\UiComponents\Adapter\AbstractWidget
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        isset($attributes['id']) ? $this->id         = $attributes['id'] : null;
        return $this;
    }

    /**
     * Force render only using template
     * 
     * @return \Antares\UiComponents\Adapter\AbstractWidget
     */
    public function templateLayout()
    {
        $this->renderOnlyWithTemplate = true;
        return $this;
    }

    /**
     * Disabled params getter
     * 
     * @return array
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * Views getter
     * 
     * @return array
     */
    public function views()
    {
        return $this->views;
    }

    /**
     * Gets snaked represenation of widget name
     * 
     * @return String
     */
    public function getSnakedName()
    {
        return snake_case(array_get($this->attributes, 'name'));
    }

}
