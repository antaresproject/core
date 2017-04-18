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

namespace Antares\Users\Http\Breadcrumb;

use DaveJamesMiller\Breadcrumbs\Facade as Breadcrumbs;
use Illuminate\Database\Eloquent\Model;

class Breadcrumb
{

    /**
     * when shows my account
     */
    public function onAccount()
    {
        if (!Breadcrumbs::exists('account')) {
            Breadcrumbs::register('account', function($breadcrumbs) {
                $breadcrumbs->push('My Account', handles('antares::foundation/account'));
            });
        }
        view()->share('breadcrumbs', Breadcrumbs::render('account'));
    }

    /**
     * on users list
     */
    public function onUsersList()
    {
        Breadcrumbs::register('users', function($breadcrumbs) {
            $breadcrumbs->push('Users', handles('antares::users/index'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('users'));
    }

    /**
     * On preview user details
     */
    public function onShowUser(Model $model)
    {
        Breadcrumbs::register('users', function($breadcrumbs) {
            $breadcrumbs->push('Users', handles('antares::users/index'), ['force_link' => true]);
        });
        view()->share('breadcrumbs', Breadcrumbs::render('users'));
    }

    /**
     * on user create or edit
     * 
     * @param Model $model
     */
    public function onCreateOrEdit(Model $model)
    {
        Breadcrumbs::register('users', function($breadcrumbs) {
            $breadcrumbs->push('Users', handles('antares::users/index'), ['force_link' => true]);
        });
        if (!$model->exists) {
            Breadcrumbs::register('user-action', function($breadcrumbs) {
                $breadcrumbs->parent('users');
                $breadcrumbs->push(trans('antares/users::messages.breadcrumbs.add'));
            });
        } else {
            Breadcrumbs::register('user-action', function($breadcrumbs) use($model) {
                $breadcrumbs->parent('users');
                $breadcrumbs->push(trans('antares/users::messages.breadcrumbs.edit', ['name' => $model->fullname]));
            });
        }
        view()->share('breadcrumbs', Breadcrumbs::render('user-action'));
    }

}
