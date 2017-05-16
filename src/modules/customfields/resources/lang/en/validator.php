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



return
        array(
            'min_checked' => 'Minimum checked',
            'max_checked' => 'Maximum checked',
            'create'      =>
            array(
                'name-non-unique'         => 'The name is incorrect. The field with the same name has already been saved.',
                'validators-empty-fields' => 'This field cannot be empty.',
            ),
            'checkboxes'  =>
            array(
                'min-checked' => 'Too few checked options',
                'max-checked' => 'validator.checkboxes.max-checked',
            ),
            'required'    => 'Required',
            'min'         => 'Minimal',
            'max'         => 'Maximum',
            'email'       => 'Email',
            'url'         => 'Url',
            'numeric'     => 'Numeric',
            'string'      => 'String',
            'ip'          => 'Ip address',
            'date'        => 'Date',
            'regex'       => 'Regular expression',
            'custom'      => 'Custom validator',
            'exists_db'   => 'Exists in database'
);
