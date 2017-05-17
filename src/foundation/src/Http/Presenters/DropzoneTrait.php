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




namespace Antares\Foundation\Http\Presenters;

trait DropzoneTrait
{

    /**
     * getting dropzone validation rules from validator
     * @param \Illuminate\Support\Facades\Validator $validator
     * @return array
     */
    protected function getValidationRules($inputName, $validator)
    {
        $validator->onUpload();
        $rules      = $validator->getValidationRules();
        $attributes = [];
        foreach ($rules[$inputName] as $rule) {
            if (strpos($rule, ':') !== FALSE) {
                list($rulename, $value) = explode(':', $rule);
                switch ($rulename) {
                    case 'mimes':
                        $separator = ',';
                        $value     = implode($separator, array_map(function($item) {
                                    return '.' . $item;
                                }, explode($separator, $value)));
                        $attributes['acceptedFiles'] = $value;
                        break;
                    case 'max':
                        $attributes['maxFilesize']   = $value / 1024;
                        break;
                    case 'min':
                        $attributes['minFilesize']   = $value / 1024;
                        break;
                }
            }
        }
        return $attributes;
    }

}
