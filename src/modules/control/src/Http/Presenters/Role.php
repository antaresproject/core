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

use Antares\Foundation\Http\Datatables\Roles as Datatables;
use Antares\Control\Http\Form\Role as RoleForm;
use Antares\Control\Http\Breadcrumb\Breadcrumb;
use Antares\Control\Contracts\ModulesAdapter;
use Antares\Model\Role as Eloquent;
use Illuminate\Container\Container;

class Role extends Presenter
{

    /**
     * application container
     * 
     * @var Container
     */
    protected $container;

    /**
     * modules adapter instance
     *
     * @var ModulesAdapter
     */
    protected $adapter;

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * datatables instance
     *
     * @var Datatables
     */
    protected $datatables;

    /**
     * Create a new Role presenter.
     * 
     * @param Container $container
     * @param ModulesAdapter $adapter
     * @param Breadcrumb $breadcrumb
     */
    public function __construct(Container $container, ModulesAdapter $adapter, Breadcrumb $breadcrumb, Datatables $datatables)
    {
        $this->container  = $container;
        $this->adapter    = $adapter;
        $this->breadcrumb = $breadcrumb;
        $this->datatables = $datatables;
    }

    /**
     * response for roles list
     * 
     * @return \Illuminate\View\View
     */
    public function table()
    {
        $this->breadcrumb->onInit();
        return $this->datatables->render('antares/control::roles.index');
    }

    /**
     * View form generator for Antares\Model\Role.
     *
     * @param  \Antares\Model\Role  $model
     *
     * @return \Antares\Contracts\Html\Form\Builder
     */
    public function form(Eloquent $model = null)
    {
        $this->breadcrumb->onRoleCreateOREdit($model);
        return new RoleForm($model);
    }

    /**
     * edit action presenter
     * 
     * @return type
     */
    public function edit(Eloquent $eloquent, array $available = array())
    {
        publish('control', ['js/control.js']);
        $id         = $eloquent->id;
        $instances  = $this->container->make('antares.acl')->all();
        $form       = $this->form($eloquent);
        $modules    = $this->adapter->modules();
        $collection = $this->container->make('antares.memory')->make('collector')->all();
        foreach ($collection as $item) {
            array_push($available, $item['aid']);
        }
        return compact('eloquent', 'form', 'modules', 'instances', 'available', 'id');
    }

    public function acl(Eloquent $eloquent)
    {
        $this->breadcrumb->onAcl($eloquent);
        app('antares.asset')->container('antares/foundation::application')->add('webpack_forms_advanced', '/webpack/forms_advanced.js', ['app_cache'])->add('webpack_acl', '/webpack/view_acl.js', ['webpack_forms_advanced']);
        $id     = $eloquent->id;
        $groups = Eloquent::query()->newQuery()->get()->all();

        return view('antares/control::roles.acl', compact('groups', 'id'));
    }

}
