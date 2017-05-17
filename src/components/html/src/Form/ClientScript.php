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


namespace Antares\Html\Form;

use Antares\Contracts\Html\Form\ClientScript as ClientScriptContract;
use Antares\Html\Adapter\FieldPermissionAdapter;
use Antares\Html\Form\Builder\AjaxValidation;
use Antares\Html\RulesDispatcher;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Input;
use Illuminate\Container\Container;

class ClientScript implements ClientScriptContract
{

    /**
     * ajax validation adapter
     * 
     * @var Antares\Contracts\Html\Form\AjaxValidation 
     */
    protected $ajaxValidation;

    /**
     * field permission adapter instance
     * 
     * @var \Antares\Html\Adapter\FieldPermissionAdapter 
     */
    protected $fieldPermissionAdapter;

    /**
     * application container
     *
     * @var Container
     */
    protected $container;

    /**
     * constructing
     */
    public function __construct(Container $container)
    {
        $this->ajaxValidation         = new AjaxValidation();
        $this->fieldPermissionAdapter = new FieldPermissionAdapter();
        $this->container              = $container;
    }

    /**
     * id generator
     * 
     * @return String
     */
    protected function generateID($prefix = 'be')
    {
        return $prefix . str_random(5);
    }

    /**
     * create client side form validator
     * 
     * @param \Antares\Contracts\Html\Grid $grid
     * @return array
     */
    public function addClientValidation(&$grid)
    {
        $rules = $grid->rules;
        $form  = $grid->attributes;
        if (!array_key_exists('id', $form)) {
            $id         = $this->generateID();
            $grid->attributes(array_merge($form, ['id' => $id]));
            $form['id'] = $id;
        }
        if (!empty($rules)) {
            $form['data-toggle'] = "validator";
            $this->attachScripts();
            $this->attachRules($grid, $rules);
        }


        if ($grid->ajaxable !== false) {
            $this->ajaxValidation->build($grid);
        }

        return ['form' => $form, 'fieldsets' => $grid->fieldsets()];
    }

    /**
     * attach rules to form controls
     * 
     * @param array $fieldsets
     * @param array $rules
     */
    protected function attachRules(&$grid, array $rules = null)
    {
        $fieldsets = $grid->fieldsets();

        if (!empty(Input::get('ajax')) && Request::ajax() && !is_null($key = Input::get('key'))) {
            $name      = Crypt::decrypt($key);
            $fieldsets = $this->fieldPermissionAdapter->resolveFields($fieldsets, $name);
            if (empty($fieldsets)) {
                return false;
            }
        }
        $controls = [];
        foreach ($fieldsets as $fieldset) {
            foreach ($fieldset->controls as $control) {
                array_push($controls, method_exists($control, 'getName') ? $control->getType() : $control->name);
            }
        }

        $rulesDispatcher = new RulesDispatcher($rules);
        $grid->rules($rulesDispatcher->getSupported($controls));

        foreach ($fieldsets as $fieldset) {
            foreach ($fieldset->controls as $control) {
                $validation          = $this->resolveRules($control, $rules);
                
                if (method_exists($control, 'setAttributes')) {
                    $control->setAttributes(array_merge($control->getAttributes(), $validation));
                } else {
                    $control->attributes = array_merge($control->attributes, $validation);
                }
            }
        }
    }

    /**
     * resolve from rules
     * 
     * @param Field $control
     * @param array $rules
     * @return array
     */
    protected function resolveRules($control, array $rules = array())
    {
        $validation         = [];
        $rulesDispatcher    = new RulesDispatcher($rules);
        $ruleName           = $rulesDispatcher->getMatchedRuleNameForControl(
            method_exists($control, 'getName') ? $control->getType() : $control->name
        );

        if ($ruleName === null) {
            return $validation;
        }

        $rule = array_get($rules, $ruleName);

        if (is_string($rule)) {
            $validation = array_merge($validation, $this->resolveRule($rule));
        }
        if (is_array($rule)) {
            foreach ($rule as $ruleAttributes) {
                $validation = array_merge($validation, $this->resolveRule($ruleAttributes));
            }
        }

        return $validation;
    }

    /**
     * resolve single form rule
     * 
     * @param String $rule
     * @return array
     */
    protected function resolveRule($rule)
    {
        $validation = [];
        switch ($rule) {
            case 'required':
                $validation['required'] = 'required';
                break;
            default:
                if (str_contains($rule, ':') && !str_contains($rule, 'unique')) {
                    list($name, $value) = explode(':', $rule);
                    $validation["data-{$name}length"] = $value;
                }
                break;
        }
        return $validation;
    }

    /**
     * attach validator client side scripts
     */
    protected function attachScripts()
    {
        $scripts   = $this->container->make('config')->get('antares/html::form.scripts.client-side');
        $container = $this->container->make('antares.asset')->container($scripts['position']);
        foreach ($scripts['resources'] as $name => $path) {
            $container->add($name, $path);
        }
        return;
    }

}
