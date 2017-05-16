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

namespace Antares\Foundation\Http\Datatables;

use Antares\Control\Http\Filter\GroupsFilter;
use Antares\Datatables\Services\DataTable;
use Antares\Support\Facades\Foundation;
use Illuminate\Support\Facades\DB;
use Antares\Support\Facades\Form;
use Antares\Model\User;

class Administrators extends DataTable
{

    /**
     * items per page
     *
     * @var mixed 
     */
    public $perPage = 25;

    /**
     * container with filters
     *
     * @var array
     */
    protected $filters = [
        GroupsFilter::class
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $builder = Foundation::make('antares.user')
                ->select(['tbl_users.id', 'tbl_users.firstname', 'tbl_users.lastname', 'tbl_users.email', 'tbl_users.created_at', 'tbl_users.status'])
                ->whereNull('tbl_users.deleted_at');
        if (config('antares/control::allow_register_with_other_roles')) {
            $builder->administrators();
        } else {
            $builder->withoutGlobalScopes()->with('roles')->whereNotNull('tbl_users.id')->whereHas('roles', function ($query) {
                $query->whereIn('tbl_roles.id', user()->roles->pluck('id')->toArray());
            });
        }

        if (!request()->ajax()) {
            $builder->where('tbl_users.status', 1);
        } else {
            $columns = request('columns');
            $status  = null;
            array_walk($columns, function($item, $index ) use(&$status) {
                if (array_get($item, 'data') == 'status') {
                    $status = array_get($item, 'search.value');
                }
            });

            if ($status == "") {
                $builder->where('tbl_users.status', 1);
            }
        }

        return $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $acl            = app('antares.acl')->make('antares/control');
        $canUpdateUser  = $acl->can('user-update');
        $canDeleteUser  = $acl->can('user-delete');
        $canLoginAsUser = $acl->can('login-as-user');

        return $this->prepare()
                        ->filter(function($query) {
                            $request = app('request');
                            $keyword = array_get($request->get('search'), 'value');
                            if (is_null($keyword) or ! strlen($keyword)) {
                                return;
                            }
                            switch ($keyword) {
                                case 'active':
                                    $query->where('tbl_users.status', 1);
                                    break;
                                case 'archived':
                                    $query->where('tbl_users.status', 0);
                                    break;
                                default:
                                    $query
                                    ->leftJoin('tbl_user_role', 'tbl_users.id', '=', 'tbl_user_role.user_id')
                                    ->leftJoin('tbl_roles', 'tbl_user_role.role_id', '=', 'tbl_roles.id')
                                    ->whereRaw("(tbl_users.firstname like '%$keyword%'  or tbl_users.lastname like '%$keyword%' or tbl_users.email like '%$keyword%' or tbl_roles.full_name like '%$keyword%')");
                                    break;
                            }
                        })
                        ->filterColumn('status', function ($query, $keyword) {
                            if ($keyword == 'all') {
                                $query->whereIn('tbl_users.status', [0, 1]);
                            } else {
                                $query->where('tbl_users.status', $keyword);
                            }
                        })
                        ->editColumn('status', function ($model) {
                            return ((int) $model->status) ? '<span class="label-basic label-basic--success">ACTIVE</span>' : '<span class="label-basic label-basic--danger">Disabled</span>';
                        })
                        ->editColumn('email', $this->getUserEmail($eloquent = null))
                        ->editColumn('role', $this->userRoles($eloquent = null))
                        ->editColumn('firstname', function($eloquent = null) {
                            return strlen($eloquent->firstname) <= 0 ? '---' : $eloquent->firstname;
                        })
                        ->editColumn('lastname', function($eloquent = null) {
                            return strlen($eloquent->lastname) <= 0 ? '---' : $eloquent->lastname;
                        })
                        ->editColumn('created_at', function ($model) {
                            return format_x_days($model->created_at);
                        })
                        ->addColumn('action', $this->getActionsColumn($canUpdateUser, $canDeleteUser, $canLoginAsUser))
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        if (!app('antares.acl')->make('antares/control')->can('roles-list')) {
            $this->filters = [];
        }
        return $this
                        ->setName('Administrators List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('antares/foundation::label.users.id')])
                        ->addColumn(['data' => 'firstname', 'name' => 'firstname', 'title' => trans('antares/foundation::label.users.firstname'), 'className' => 'bolded'])
                        ->addColumn(['data' => 'lastname', 'name' => 'lastname', 'title' => trans('antares/foundation::label.users.lastname'), 'className' => 'bolded'])
                        ->addColumn(['data' => 'email', 'name' => 'email', 'title' => trans('antares/foundation::label.users.email'), 'className' => 'bolded'])
                        ->addColumn(['data' => 'role', 'name' => 'role', 'title' => trans('antares/foundation::label.users.role')])
                        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => trans('antares/foundation::label.users.created_at')])
                        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => trans('antares/foundation::label.users.status')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->setDeferedData()
                        ->addGroupSelect($this->statuses(), 6, 'all');
    }

    /**
     * Scopes roles by configuration
     * 
     * @param \Illuminate\Database\Query\Builder $builder
     * @return \Illuminate\Database\Query\Builder
     */
    protected function scopeRoles(&$builder)
    {
        return (config('antares/control::allow_register_with_other_roles')) ? $builder->administrators() :
                $builder->with('roles')->whereNotNull('tbl_users.id')->whereHas('roles', function ($query) {
                    $query->whereIn('tbl_roles.id', user()->roles->pluck('id')->toArray());
                });
    }

    /**
     * Creates select for statuses
     * 
     * @return String
     */
    protected function statuses()
    {

        $statuses = User::withoutGlobalScopes()
                ->select([DB::raw('count(id) as counter'), 'status'])
                ->whereNull('tbl_users.deleted_at');
        $this->scopeRoles($statuses);
        $result   = $statuses->groupBy('status')->get()->pluck('counter', 'status')->toArray();
        return [
            'all' => trans('antares/foundation::messages.statuses.all'),
            0     => trans('antares/foundation::messages.statuses.disabled', ['count' => array_get($result, 0, 0)]),
            1     => trans('antares/foundation::messages.statuses.active', ['count' => array_get($result, 1, 0)])
        ];
    }

    /**
     * Get actions column for table builder.
     *
     * @return callable
     */
    protected function getActionsColumn($canUpdateUser, $canDeleteUser, $canLoginAsUser)
    {
        return function ($row) use($canUpdateUser, $canDeleteUser, $canLoginAsUser) {
            $html               = app('html');
            $this->tableActions = [];
            $user               = user();

            if ($canUpdateUser) {
                $url = ($user->id === $row->id) ? 'antares/foundation::/account' : "antares::control/users/{$row->id}/edit";
                $this->addTableAction('edit', $row, $html->link(handles($url), trans('antares/foundation::label.edit'), ['data-icon' => 'edit']));
            }
            if (!is_null($user) && $user->id !== $row->id and $canDeleteUser) {
                $this->addTableAction('delete', $row, $html->create('li', $html->link(handles("antares::control/users/{$row->id}/delete", ['csrf' => true]), trans('antares/foundation::label.delete'), [
                                    'class'            => 'triggerable confirm',
                                    'data-icon'        => 'delete',
                                    'data-title'       => trans("antares/control::messages.users.delete.are_you_sure"),
                                    'data-description' => trans("antares/control::messages.users.delete.deleteing_description", ["fullname" => $row->fullname])
                ])));
            }
            if (!is_null($user) && $user->id !== $row->id and $canLoginAsUser && $row->roles->pluck('name')->toArray() !== $user->roles->pluck('name')->toArray()) {

                $this->addTableAction('login_as', $row, $html->create('li', $html->link(handles("login/with/{$row->id}"), trans('antares/control::label.login_as', ['fullname' => $row->fullname]), [
                                    'class'            => 'triggerable confirm',
                                    'data-icon'        => 'odnoklassniki',
                                    'data-title'       => trans("Are you sure?"),
                                    'data-description' => trans('antares/control::label.login_as', ['fullname' => $row->fullname])
                ])));
            }
            if (empty($this->tableActions)) {
                return '';
            }
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $this->tableActions->toArray())))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

    /**
     * Get email column for table builder.
     *
     * @return callable
     */
    protected function getUserEmail($row)
    {
        return function ($row) {
            return app('html')->create('span', e($row->email))->get();
        };
    }

    /**
     * Get user roles column for table builder.
     *
     * @return callable
     */
    protected function userRoles($row)
    {
        return function ($row) {

            $roles = $row->roles;
            $value = [];

            foreach ($roles as $role) {
                $value[] = app('html')->create('span', e($role->full_name), [
                    'class' => 'label-basic label-basic--info'
                ]);
            }
            return implode('', [app('html')->create('span', app('html')->raw(implode(' ', $value)), ['class' => 'meta']),]);
        };
    }

}
