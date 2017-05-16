<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\Events;

use Antares\Html\Form\Grid;

class FormValidate
{

    /**
     * fire event to attach customfields rules to form grid
     * 
     * @param Grid $grid
     * @return boolean
     */
    public function handle(Grid $grid)
    {
        $name      = $grid->name;
        $extension = app('antares.extension')->getActualExtension();

        if (is_null($name) or is_null($extension)) {
            return false;
        }
        $namespace = $extension . '.' . $name;
        if (is_null(app('antares.memory')->make('registry')->get($namespace))) {
            return false;
        }
        $gridRules = !is_array($grid->rules) ? [] : $grid->rules;
        $rules     = array_merge($gridRules, $this->getRulesForCustomFields($namespace));
        $grid->rules($rules);
        return true;
    }

    /**
     * get rules for customfields
     * 
     * @param String $namespace
     * @return array
     */
    protected function getRulesForCustomFields($namespace)
    {
        $rules      = [];
        $brand      = antares('memory')->get('brand.default');
        $collection = app('antares.customfields.model.view')->query()->where('namespace', $namespace)->where('brand_id', $brand)->get();
        if ($collection->isEmpty()) {
            return $rules;
        }
        $collection->each(function($item) use(&$rules) {

            $validators = [];
            $item->config->each(function($config) use(&$validators) {
                $name          = $config->validator->name;
                $name == 'regex' && $config->value = implode('', ['[', $config->value, ']']);
                $rule          = ((!is_null($config->value) ? ':' . $config->value : ''));
                $validators[]  = $name . $rule;
            });

            $rules[$item->name] = $validators;
        });
        return $rules;
    }

}
