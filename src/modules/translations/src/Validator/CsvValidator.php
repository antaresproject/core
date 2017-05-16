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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Translations\Validator;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Events\Dispatcher;
use Antares\Support\Validator;
use Illuminate\Support\Facades\Validator as SupportValidator;

class CsvValidator extends Validator
{

    public function __construct(Factory $factory, Dispatcher $dispatcher)
    {
        parent::__construct($factory, $dispatcher);
        SupportValidator::extend('source', 'Antares\Translations\Validator\CsvCustomValidator@file');
    }

    /**
     * On upload validations.
     * @return void
     */
    public function onUpload()
    {
        $this->rules['file'] = ['required', 'max:10000', 'min:1', 'file'];
    }

}
