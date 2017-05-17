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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Users\Http\Form;

use Antares\Users\Validation\PictureValidator;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Asset\JavaScriptExpression;
use Antares\Html\Form\Grid;

class Account extends Grid
{

    /**
     * constructing
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct($model)
    {
        parent::__construct(app());
        $this->name('Profile');
        (auth()->guest()) ? $this->resourced(handles('register'), $model) : $this->simple(handles('antares/foundation::account'), [], $model);
        $this->hidden('id');
        if (auth()->guest()) {
            $this->layout('antares/foundation::credential.partials._form');
        }
        $this->fieldset(function (Fieldset $fieldset) {
            $legend = auth()->guest() ? trans('antares/foundation::title.users.profile_details') : trans('antares/foundation::title.users.account_details');

            $fieldset->legend($legend);

            $fieldset->control('input:text', 'email')
                    ->label(trans('antares/foundation::label.users.email'))
                    ->attributes(['class' => 'mdl-textfield__input'])
                    ->wrapper(['class' => 'w250']);

            $fieldset->control('input:text', 'firstname')
                    ->label(trans('antares/foundation::label.users.firstname'))
                    ->attributes(['class' => 'mdl-textfield__input'])
                    ->wrapper(['class' => 'w270']);

            $fieldset->control('input:text', 'lastname')
                    ->label(trans('antares/foundation::label.users.lastname'))
                    ->attributes(['class' => 'mdl-textfield__input'])
                    ->wrapper(['class' => 'w270']);

            $fieldset->control('input:password', 'password')
                    ->label(trans('antares/foundation::label.users.password'))
                    ->attributes(['class' => 'mdl-textfield__input'])
                    ->wrapper(['class' => 'w300']);

            $fieldset->control('input:password', 'password_confirmation')
                    ->label(trans('antares/foundation::label.users.password_retype'))
                    ->attributes(['class' => 'mdl-textfield__input'])
                    ->wrapper(['class' => 'w300']);


            $control = $fieldset->control('button', 'button')
                    ->attributes([
                'type'  => 'submit',
                'class' => 'btn btn--submit btn--s-xxl btn--primary mdl-button mdl-js-button mdl-js-ripple-effect'
            ]);

            (!auth()->guest()) ? $control->value(trans('antares/foundation::label.save_changes')) : $control->value(trans('antares/foundation::label.users.register'));

            if (auth()->guest()) {
                $fieldset->control('button', 'login')
                        ->field(function() {
                            return app('html')->link(handles("antares::/"), trans('antares/foundation::label.users.login'));
                        });
            }
        });
        if (!auth()->guest()) {
            $this->fieldset(function (Fieldset $fieldset) {
                $fieldset->legend(trans('antares/foundation::title.users.profile_picture'));
                $url = handles("antares/foundation::account/picture");
                $fieldset->control('dropzone', 'picture')
                        ->attributes($this->dropzoneAttributes($url))
                        ->fieldClass('input-dropzone')
                        ->label(trans('antares/foundation::label.users.select_file'));
            });
        }

        $this->rules([
            'email'                 => ['required', 'email', 'unique:tbl_users,email'],
            'fullname'              => ['required', 'min:3', 'max:255'],
            'password'              => ['required', 'min:3', 'max:20', 'confirmed'],
            'password_confirmation' => ['required', 'min:3', 'max:20']
        ]);
    }

    /**
     * dropzone attributes creator
     * 
     * @param String $url
     * @return array
     */
    protected function dropzoneAttributes($url)
    {
        $extensions    = app(PictureValidator::class)->getValidExtensions();
        $acceptedFiles = implode(',', array_map(function($current) {
                    return '.' . $current;
                }, $extensions));

        $init = new JavaScriptExpression($this->init('profilePicture'));
        return [
            'container'     => 'profilePicture',
            'paramName'     => 'file',
            'view'          => 'antares/foundation::users.partials._dropzone',
            'url'           => $url,
            'maxFiles'      => 1,
            'acceptedFiles' => $acceptedFiles,
            'init'          => $init];
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
                this.on("addedfile", function() {
                    if (this.files[1]!=null){
                        this.removeFile(this.files[0]);
                    }
                });
            }
        
CBALL;
    }

}
