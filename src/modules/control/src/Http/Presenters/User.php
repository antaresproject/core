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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Control\Http\Presenters;

use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Foundation\Http\Datatables\Administrators;
use Antares\Contracts\Html\Form\Grid as FormGrid;
use Antares\Control\Http\Breadcrumb\Breadcrumb;
use Illuminate\Contracts\Auth\Authenticatable;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Contracts\Html\Form\Builder;
use Illuminate\Support\Facades\Event;
use Illuminate\Contracts\Auth\Guard;

class User extends Presenter
{

    /**
     * Current logged in user contract implementation.
     *
     * @var Authenticatable|null
     */
    protected $user;

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * Administrators datatable instance
     *
     * @var Administrators 
     */
    protected $datatables;

    /**
     * Construct a new User presenter.
     *
     * @param  Guard  $auth
     * @param  FormFactory  $form
     * @param Breadcrumb $breadcrumb
     */
    public function __construct(Guard $auth, FormFactory $form, Breadcrumb $breadcrumb, Administrators $datatables)
    {
        $this->user       = $auth->user();
        $this->form       = $form;
        $this->breadcrumb = $breadcrumb;
        $this->datatables = $datatables;
    }

    /**
     * Table View Generator for Antares\Model\Brands.
     * @return String
     */
    public function table()
    {
        $this->breadcrumb->onInit();
        return $this->datatables->render('antares/control::users.index');
    }

    /**
     * Form View Generator for Antares\Model\User.
     *
     * @param  User  $model
     *
     * @return Builder
     */
    public function form($model)
    {
        $this->breadcrumb->onUserCreateOrEdit($model);
        return $this->form->of('antares.users', function (FormGrid $form) use ($model) {
                    $form->name('user.form');
                    $form->resource($this, 'antares::control/users', $model);
                    $form->hidden('id');
                    $form->fieldset('User fields', function (Fieldset $fieldset) use($model) {
                        $fieldset->control('input:text', 'email')
                                ->label(trans('antares/foundation::label.users.email'));

                        $fieldset->control('input:text', 'firstname')
                                ->label(trans('antares/foundation::label.users.firstname'));

                        $fieldset->control('input:text', 'lastname')
                                ->label(trans('antares/foundation::label.users.lastname'));

                        $fieldset->control('input:password', 'password')
                                ->label(trans('antares/foundation::label.users.password'));

                        if ($model->id != user()->id) {
                            $status = $fieldset->control('input:checkbox', 'status')
                                    ->label(trans('antares/foundation::label.users.active'))
                                    ->value(1);
                            if ($model->status) {
                                $status->checked();
                            }
                        }

                        $fieldset->control('select', 'roles[]')
                                ->label(trans('antares/foundation::label.users.roles'))
                                ->options(function () {
                                    $roles = app('antares.role');
                                    if (config('antares/control::allow_register_with_other_roles')) {
                                        return $roles->managers()->pluck('name', 'id');
                                    } else {
                                        return user()->roles->pluck('name', 'id');
                                    }
                                })
                                ->value(function ($row) {
                                    $roles = [];
                                    foreach ($row->roles as $row) {
                                        $roles[] = $row->id;
                                    }
                                    return $roles;
                                });
                        $fieldset->control('button', 'button')
                                ->attributes(['type' => 'submit', 'class' => 'btn btn--md btn--primary mdl-button mdl-js-button mdl-js-ripple-effect'])
                                ->value(trans('antares/foundation::label.save_changes'));

                        $fieldset->control('button', 'cancel')
                                ->field(function() {
                                    return app('html')->link(handles("antares::control/index/users"), trans('antares/foundation::label.cancel'), ['class' => 'btn btn--md btn--default mdl-button mdl-js-button']);
                                });
                    });
                    $form->rules([
                        'email'     => ['required', 'email', 'unique:tbl_users,email' . ((!$model->exists) ? '' : ',' . $model->id)],
                        'firstname' => ['required'],
                        'lastname'  => ['required'],
                        'roles'     => ['required'],
                    ]);
                });
    }

    /**
     * Fire Event related to eloquent process.
     *
     * @param  string  $type
     * @param  array   $parameters
     *
     * @return void
     */
    protected function fireEvent($type, array $parameters = [])
    {
        Event::fire("antares.{$type}: users", $parameters);
        Event::fire("antares.{$type}: user.account", $parameters);
    }

}
