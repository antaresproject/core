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

namespace Antares\Translations\Http\Form;

use Antares\Html\Form\Fieldset;
use Antares\Html\Form\Grid;

class Language extends Grid
{

    /**
     * constructing
     */
    public function __construct()
    {
        parent::__construct(app());
        $this->name('Language form');
        $this->simple(handles('antares::translations/languages/add'), ['id' => 'language-form']);
        $this->fieldset(function (Fieldset $fieldset) {
            $fieldset->control('input:text', 'code')
                    ->label(trans('Code'))
                    ->attributes(['size' => 2, 'class' => 'w100']);

            $fieldset->control('input:text', 'name')
                    ->label(trans('Name'))
                    ->attributes(['class' => 'w200']);
        });

        $this->fieldset(function (Fieldset $fieldset) {
            $fieldset->control('button', 'cancel')
                    ->field(function() {
                        return app('html')->link(handles("antares::translations/languages/index"), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                    });
            $fieldset->control('button', 'button')
                    ->attributes(['type' => 'submit', 'class' => 'btn btn-primary'])
                    ->value(trans('antares/foundation::label.save_changes'));
        });



        $this->rules([
            'code' => ['required', 'unique:tbl_languages,code', 'max:2', 'min:2'],
            'name' => ['required', 'unique:tbl_languages,name', 'min:2'],
        ]);
        $this->ajaxable();
    }

}
