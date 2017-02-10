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


namespace Antares\Users\Http\Presenters;

use Antares\Contracts\Html\Form\Fieldset;
use Antares\Contracts\Html\Form\Grid as FormGrid;
use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Users\Http\Form\Account as AccountForm;
use Illuminate\Support\Facades\Event;
use Antares\Tester\Traits\TestableTrait;
use Antares\Users\Http\Breadcrumb\Breadcrumb;

class Account extends Presenter
{

    use TestableTrait;

    /**
     * breadcrumb instance
     *
     * @var Breadcrumb 
     */
    protected $breadcrumb;

    /**
     * Construct a new Account presenter.
     *
     * @param  \Antares\Contracts\Html\Form\Factory  $form
     */
    public function __construct(FormFactory $form, Breadcrumb $breadcrumb)
    {
        $this->form       = $form;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * Form view generator for User Account.
     *
     * @param  \Antares\Model\User  $model
     * @return \Antares\Contracts\Html\Form\Builder
     */
    public function profile($model)
    {
        $this->breadcrumb->onAccount();
        return app('antares.form')->add('antares.account', new AccountForm($model));
    }

    /**
     * Form view generator for user account edit password.
     *
     * @param  \Antares\Model\User  $model
     *
     * @return \Antares\Contracts\Html\Form\Builder
     */
    public function password($model)
    {
        Event::fire('antares.forms', 'user.password');
        return $this->form->of('antares.account: password', function (FormGrid $form) use ($model) {
                    $form->setup($this, 'antares::account/password', $model);
                    $form->hidden('id');

                    $form->fieldset(function (Fieldset $fieldset) {
                        $fieldset->control('input:password', 'current_password')
                                ->label(trans('antares/foundation::label.account.current_password'));

                        $fieldset->control('input:password', 'new_password')
                                ->label(trans('antares/foundation::label.account.new_password'));

                        $fieldset->control('input:password', 'confirm_password')
                                ->label(trans('antares/foundation::label.account.confirm_password'));
                    });
                    $form->tester('Password', [
                        'title'     => 'User Password Test',
                        'validator' => 'Antares\Domains\Dns\Tester\CPanelTester'
                    ]);
                });
    }

}
