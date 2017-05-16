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

namespace Antares\Translations\Listener;

use Antares\Contracts\Html\Form\Fieldset;
use Illuminate\Support\Facades\Input;
use Illuminate\Events\Dispatcher;
use Antares\Model\UserMeta;

class AccountListener
{

    /**
     * event dispatcher
     *
     * @var Dispatcher 
     */
    protected $dispatcher;

    /**
     * Constructing
     * 
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Appends account user form with language select field
     * 
     * @return \Antares\Translations\Listener\AccountListener
     */
    public function listenForm()
    {

        $this->dispatcher->listen('antares.form: profile.*', function($name, array $params = []) {

            $row     = $params[0];
            $builder = $params[1];

            $builder->grid->fieldset(function (Fieldset $fieldset) {
                $fieldset->legend(trans('antares/translations::messages.language_legend'));
                $langs = app('languages')->langs()->pluck('name', 'code');
                $fieldset->control('select', 'language')
                        ->label(trans('antares/translations::messages.default_language_label'))
                        ->attributes(['data-flag-select', 'data-selectAR' => true, 'class' => 'w220'])
                        ->fieldClass('input-field--icon')
                        ->prepend('<span class="input-field__icon"><span class="flag-icon"></span></span>')
                        ->optionsData(function() use($langs) {
                            $codes  = $langs->keys()->toArray();
                            $return = [];
                            foreach ($codes as $code) {
                                $flag = $code == 'en' ? 'us' : $code;
                                array_set($return, $code, ['country' => $flag]);
                            }
                            return $return;
                        })
                        ->value(user_meta('language', 'en'))
                        ->options($langs);
            });
        });
        return $this;
    }

    /**
     * Listen for event, when user changes language
     * 
     * @return \Antares\Translations\Listener\AccountListener
     */
    public function listenFormSave()
    {
        $this->dispatcher->listen('eloquent.saved: App\User', function($model) {
            if (!is_null($language = Input::get('language'))) {
                $uid         = auth()->user()->id;
                $meta        = UserMeta::firstOrNew(['user_id' => $uid, 'name' => 'language']);
                $meta->value = $language;
                $meta->save();
            }
        });
        return $this;
    }

}
