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

use Antares\Support\Facades\Foundation;
use Illuminate\Support\Fluent;

class ValidatorHandler
{

    /**
     * Handle `antares.form: customfields.validate` event.
     * @param Fluent $rules
     * @param Fluent $attributes
     * @return void
     */
    public function onSubmitForm(Fluent $rules, Fluent $attributes)
    {
        $name = $attributes->get('name');
        if (is_null($name)) {
            return;
        }
        $brand      = antares('memory')->get('brand.default');
        $collection = Foundation::make('antares.customfields.model.view')->query()->where('namespace', $name)->where('brand_id', $brand)->get();
        if ($collection->isEmpty()) {
            return;
        }
        $collection->each(function($item) use(&$rules) {

            $validators = [];
            $item->config->each(function($config) use(&$validators) {
                $name          = $config->validator->name;
                $name == 'regex' && $config->value = implode('', ['[', $config->value, ']']);
                $rule          = ((!is_null($config->value) ? ':' . $config->value : ''));
                $validators[]  = $name . $rule;
            });
            $rules->{$item->name} = $validators;
        });
    }

}
