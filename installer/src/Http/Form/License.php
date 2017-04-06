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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Installation\Http\Form;

use Antares\Installer\Validator\LicenseValidator;
use Illuminate\Support\Facades\Validator;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Html\Form\Grid as FormGrid;
use function trans;
use function app;

class License
{

    public static function getInstance()
    {
        return app('antares.form')->of("antares.license")->extend(function (FormGrid $form) {
                    $form->name('License form');
                    $form->layout('antares/installer::partials._form');
                    $form->attributes(['enctype' => 'multipart/form-data']);
                    $enabled = config('license.enabled');
                    $form->fieldset(trans('License Details'), function (Fieldset $fieldset) use($enabled) {
                        $keyAttributes = ['placeholder' => 'provide license key here...', 'class' => 'w470'];
                        if (!$enabled) {
                            array_set($keyAttributes, 'disabled', 'disabled');
                        }
                        $fieldset->control('input:text', 'license_key')
                                ->label(trans('License key: '))
                                ->attributes($keyAttributes);

                        $fileAttributes = ['id' => 'input-upload', 'class' => 'input-upload'];
                        if (!$enabled) {
                            array_set($fileAttributes, 'disabled', 'disabled');
                        }
                        $fieldset->control('input:file', 'license_file')
                                ->label(trans('License file: '))
                                ->attributes($fileAttributes);
                    });
                    if ($enabled) {
                        Validator::resolver(function($translator, $data, $rules, $messages) {
                            return new LicenseValidator($translator, $data, $rules, $messages);
                        });
                        $form->rules([
                            'license_key' => ['required', 'max:4000', 'min:1', 'certificate']
                        ]);
                    }
                });
    }

}
