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

use Illuminate\Validation\Validator;

class CsvCustomValidator extends Validator
{

    /**
     * validates csv file
     * 
     * @param string $attribute
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $value
     * @param array $parameters
     * @return boolean
     */
    public function validateFile($attribute, $value)
    {
        $extension = $value->getClientOriginalExtension();
        if ($extension !== 'csv') {
            $this->setCustomMessages([$attribute => trans('File has invalid extension')]);
            $this->addFailure($attribute, $attribute, []);
            return false;
        }

        return true;
    }

}
