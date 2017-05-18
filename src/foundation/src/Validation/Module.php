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




namespace Antares\Foundation\Validation;

use Antares\Support\Validator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Validator as SupportValidator;

class Module extends Validator
{

    public function __construct(Factory $factory, Dispatcher $dispatcher)
    {
        parent::__construct($factory, $dispatcher);
        SupportValidator::extend('source', 'Antares\Foundation\Validation\Module@source');
    }

    /**
     * On upload validations.
     * @return void
     */
    public function onUpload()
    {
        $this->rules['file'] = ['mimes:zip', 'max:10000', 'min:1', 'source'];
    }

}
