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

namespace Antares\Control\Http\Breadcrumb;

use DaveJamesMiller\Breadcrumbs\Facade as Breadcrumbs;
use Illuminate\Database\Eloquent\Model;

class Breadcrumb
{

    /**
     * Init breadcrumbs
     */
    public function onInit()
    {
        Breadcrumbs::register('staff', function($breadcrumbs) {
            $breadcrumbs->push(trans('antares/foundation::title.staff'), handles('antares::control/index/roles'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('staff'));
    }

    /**
     * on roles list
     */
    public function onList()
    {
        $this->onInit();
        Breadcrumbs::register('roles-list', function($breadcrumbs) {
            $breadcrumbs->parent('staff');
            $breadcrumbs->push(trans('antares/control::title.breadcrumbs.groups'), handles('antares::control/index/roles'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('roles-list'));
    }

    /**
     * on create or edit role
     * 
     * @param Model $model
     */
    public function onRoleCreateOrEdit(Model $model = null)
    {
        $this->onList();
        Breadcrumbs::register('edit-role', function($breadcrumbs) use($model) {
            $breadcrumbs->parent('roles-list');
            $exists = is_null($model) ? false : $model->exists;
            $name   = ($exists) ? 'Edit Group ' . $model->full_name : 'Add Group';
            $breadcrumbs->push($name, '#');
        });
        view()->share('breadcrumbs', Breadcrumbs::render('edit-role'));
    }

    /**
     * On users list
     */
    protected function onUsersList()
    {
        $this->onInit();

        Breadcrumbs::register('users-list', function($breadcrumbs) {
            $breadcrumbs->parent('staff');
            $breadcrumbs->push(trans('antares/control::title.breadcrumbs.users'), handles('antares::control/index/users'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('users-list'));
    }

    /**
     * when shows roles list
     */
    public function onAdminsList()
    {
        $this->onInit();
        Breadcrumbs::register('admins-list', function($breadcrumbs) {
            $breadcrumbs->parent('staff');
            $breadcrumbs->push(trans('antares/control::title.breadcrumbs.users'), handles('antares::control/index/users'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('admins-list'));
    }

    /**
     * on user create or edit
     * 
     * @param Model $model
     */
    public function onUserCreateOrEdit(Model $model)
    {
        $this->onUsersList();
        $name = $model->exists ? 'User Edit ' . $model->fullname : 'User Add';
        Breadcrumbs::register('user', function($breadcrumbs) use($name) {
            $breadcrumbs->parent('users-list');
            $breadcrumbs->push($name, '#');
        });
        view()->share('breadcrumbs', Breadcrumbs::render('user'));
    }

    /**
     * On areas list
     */
    public function onAreasList()
    {

        $this->onInit();
        Breadcrumbs::register('areas-list', function($breadcrumbs) {
            $breadcrumbs->parent('staff');
            $breadcrumbs->push(trans('antares/control::messages.areas'), handles('antares::control/areas/index'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('areas-list'));
    }

    /**
     * On area create
     */
    public function onAreaCreate()
    {
        $this->onAreasList();
        Breadcrumbs::register('area-create', function($breadcrumbs) {
            $breadcrumbs->parent('areas-list');
            $breadcrumbs->push(trans('antares/control::messages.area_add'), '#');
        });
        view()->share('breadcrumbs', Breadcrumbs::render('area-create'));
    }

    /**
     * On area edit
     */
    public function onAreaEdit(Model $model)
    {
        $this->onAreasList();
        Breadcrumbs::register('area-edit', function($breadcrumbs) use($model) {
            $breadcrumbs->parent('areas-list');
            $breadcrumbs->push(trans('antares/control::messages.area_edit', ['name' => $model->getHumanReadableName()]), '#');
        });
        view()->share('breadcrumbs', Breadcrumbs::render('area-edit'));
    }

    /**
     * On area edit
     */
    public function onAcl(Model $model)
    {
        $this->onList();
        Breadcrumbs::register('edit-acl', function($breadcrumbs) use($model) {
            $breadcrumbs->parent('roles-list');
            $breadcrumbs->push(trans('antares/control::messages.acl_rules', ['fullname' => $model->full_name]));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('edit-acl'));
    }

}
