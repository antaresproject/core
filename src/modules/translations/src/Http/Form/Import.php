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

use Antares\Foundation\Http\Presenters\DropzoneTrait;
use Antares\Translations\Validator\CsvValidator;
use Antares\Asset\JavaScriptExpression;
use Antares\Html\Form\Fieldset;
use Antares\Html\Form\Grid;

class Import extends Grid
{

    use DropzoneTrait;

    /**
     * constructing
     */
    public function __construct()
    {
        parent::__construct(app());

        $this->name('Language files importer');
        $locale = from_route('locale');
        $type   = from_route('type');
        $url    = handles("antares::translations/languages/import/{$locale}/{$type}");
        $this->simple($url, ['enctype' => 'multipart/form-data']);

        $this->fieldset(function (Fieldset $fieldset) use($url, $locale, $type) {
            $fieldset->control('dropzone', 'csv')
                    ->attributes($this->dropzoneAttributes($url))
                    ->label('Select csv file')
                    ->help('*Only csv files are accepted');

            $fieldset->control('button', 'cancel')
                    ->field(function() use($locale, $type) {
                        return app('html')->link(handles("antares::translations/index/" . $type . "/" . $locale), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                    });
            $fieldset->control('button', 'button')
                    ->attributes(['type' => 'submit', 'class' => 'btn btn-primary'])
                    ->value(trans('antares/translations::messages.import'));
        });
    }

    /**
     * dropzone attributes creator
     * 
     * @param String $url
     * @return array
     */
    protected function dropzoneAttributes($url)
    {

        $init      = new JavaScriptExpression($this->init('translationImport'));
        $validator = app(CsvValidator::class);
        $rules     = $this->getValidationRules('file', $validator);
        return [
            'container'     => 'translationImport',
            'paramName'     => 'file',
            'view'          => 'antares/translations::admin.partials._dropzone',
            'url'           => $url,
            'maxFiles'      => 1,
            'acceptedFiles' => '.csv',
            'init'          => $init] + $rules;
    }

    /**
     * default on success upload
     * 
     * @param String $container
     * @return String
     */
    protected function init($container)
    {
        return <<<CBALL
            function(){  
                this.on("success", function (file, response) {
                        $('#$container').parent().find('input[name=file]').val(response.path);
                });
            }
        
CBALL;
    }

}
