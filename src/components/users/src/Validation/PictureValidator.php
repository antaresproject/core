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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Users\Validation;

use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Http\UploadedFile;
use Antares\Support\Validator;

class PictureValidator extends Validator
{

    /**
     * Acceptable picture extensions
     *
     * @var array 
     */
    protected $extensions = ['png', 'jpg', 'jpeg'];

    /**
     * Available validation phrases
     *
     * @var type 
     */
    protected $phrases = [];

    /**
     * On upload validations.
     * 
     * @return void
     */
    public function onUpload()
    {
        ValidatorFacade::extend('picture', 'Antares\Users\Validation\PictureValidator@picture');
        $this->phrases['picture'] = trans('antares/users::validation.picture_invalid_extensions', ['extensions' => implode(', ', $this->extensions)]);
        $this->rules['file']      = ['required', 'max:10000', 'min:1', 'file', 'picture'];
    }

    /**
     * Validates picture extension
     * 
     * @param String $attribute
     * @param \Illuminate\Http\UploadedFile $value
     * @return boolean
     */
    public function picture($attribute, UploadedFile $value)
    {
        $extension = $value->getClientOriginalExtension();
        return in_array($extension, $this->extensions);
    }

    /**
     * Valid extensions getter
     * 
     * @return array
     */
    public function getValidExtensions()
    {
        return $this->extensions;
    }

}
