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


namespace Antares\Html\Form\Builder;

use Antares\Contracts\Html\Form\AjaxValidation as AjaxValidationContract;
use Antares\Contracts\Html\Form\Field as FieldContract;
use Antares\Asset\JavaScriptDecorator;
use Antares\Contracts\Html\Grid;
use Antares\Form\Controls\AbstractType;
use Closure;

class AjaxValidation implements AjaxValidationContract
{

    /**
     * list of default ajaxable validation attributes
     * 
     * @var array
     */
    protected $attributes;

    /**
     * scripts configuration
     *
     * @var array
     */
    protected $scripts;

    /**
     * constructing
     */
    public function __construct()
    {
        $config           = app('config')->get('antares/html::form');
        $this->attributes = array_get($config, 'validator.ajaxable');
        $this->scripts    = array_get($config, 'scripts.ajax-side');
    }

    /**
     * id generator
     * 
     * @return String
     */
    protected function generateID($prefix = 'module')
    {
        return $prefix . str_random(5);
    }

    /**
     * build ajax validation scripts
     * 
     * @param Grid $grid
     * @return boolean
     */
    public function build(Grid &$grid)
    {
        $attributes = array_merge($this->attributes, $grid->ajaxable);
        $inputs     = [];
        $fieldsets  = $grid->fieldsets();

        $name = null;
        foreach ($fieldsets as $index => $fieldset) {

            if ($index <= 0) {
                $name = ($fieldset->name instanceof Closure) ? 'fieldset_' . $index : snake_case($fieldset->name, '-');
            }
            foreach ($fieldset->controls as $control) {
                $id = method_exists($control, 'getId') ? $control->getId() : $control->id;
                
                $inputId     = (is_null($id) OR strlen($id) <= 0) ? $this->generateID('input') : $id;
                $id          = str_replace(['[', ']'], '_', $inputId);
                $inputs[$id] = $this->field($control);
                
                if (method_exists($control, 'getId')) {
                    $control->setId($id);
                } else {
                    $control->id = $id;
                }
            }
        }

        $attributes['attributes'] = array_values($inputs);
        $id                       = $grid->attributes['id'];
        if (isset($attributes['summaryID'])) {
            $attributes['summaryID'] = $attributes['summaryID'] . $id;
        }
        $script = $this->inline($id, $attributes, $grid->attributes['url']);
        $this->attachScripts($id, $script);

        return $grid;
    }

    /**
     * attaches validator scripts
     * 
     * @param numeric | mixed $id
     * @param String $script
     */
    protected function attachScripts($id = null, $script = null)
    {
        if (!is_null($id) && !is_null($script)) {
            $container = app('antares.asset')->container(array_get($this->scripts, 'position'));
            foreach (array_get($this->scripts, 'resources') as $name => $path) {
                $container->add($name, $path);
            }

            $container->inlineScript("active-form-{$id}", $script);
        }
    }

    /**
     * 
     * create inline script of form validation
     * 
     * @param numeric | mixed $id
     * @param array $attributes
     * @return String
     */
    protected function inline($id = null, array $attributes = null, $url = null)
    {
        if (!is_null($id) && !is_null($attributes)) {
            $decorated = JavaScriptDecorator::decorate($attributes);

            $script = <<<EOD
$(document).ready(function() {
    $('#%s').yiiactiveform(%s);
  });
EOD;
            return sprintf($script, $id, $decorated);
        }
        return '';
    }

    /**
     * setting field parameters
     * 
     * @param FieldContract|AbstractType $field
     * @return array
     */
    protected function field($field)
    {
        $id = $field instanceof FieldContract ? $field->id : $field->getId();
        return [
            'id'                   => $id,
            'inputID'              => $id,
            'name'                 => $field instanceof FieldContract ? $field->name : $field->getName(),
            'errorID'              => $id . '_error',
            'enableAjaxValidation' => true,
            'summary'              => true,
            'inputContainer'       => 'div.form-group',
        ];
    }

}
