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

namespace Antares\Users\Http\Datatables;

use Antares\Users\Http\Filter\UserCreatedAtFilter;
use Antares\Datatables\Services\DataTable;
use Antares\Support\Facades\Foundation;
use Illuminate\Support\Facades\DB;
use Antares\Support\Facades\Form;
use Antares\Support\Facades\HTML;
use Antares\Model\User;

class Users extends DataTable
{

    /**
     * hwo many rows per page
     *
     * @var mixed
     */
    public $perPage = 25;

    /**
     * Filters definition
     *
     * @var array
     */
    protected $filters = [
        UserCreatedAtFilter::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $query = Foundation::make('antares.user')->members()->select([
            'id',
            'firstname',
            'lastname',
            'email',
            'created_at',
            'status',
        ]);

        if (request()->ajax()) {
            $columns = request()->get('columns');
            $all     = array_where($columns, function ($index, $item) {
                return array_get($item, 'data') == 'status' && array_get($item, 'search.value') == 'all';
            });
            if (!empty($all)) {
                $query->whereIn('status', [0, 1]);
            }
        }



        return $query;
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
                        ->filterColumn('firstname', function ($query, $keyword) {
                            $query->where('firstname', 'like', "%$keyword%");
                        })
                        ->filterColumn('lastname', function ($query, $keyword) {
                            $query->where('lastname', 'like', "%$keyword%");
                        })
                        ->filterColumn('email', function ($query, $keyword) {
                            $query->where('email', 'like', "%$keyword%");
                        })
                        ->filterColumn('status', function ($query, $keyword) {
                            $value = null;
                            switch ($keyword) {
                                case 'archived':
                                    $value = 0;
                                    break;
                                case 'active':
                                    $value = 1;
                                    break;
                                default:
                                    if (is_numeric($keyword) && $keyword <= 1) {
                                        $value = $keyword;
                                    }
                                    break;
                            }
                            if (!is_null($value)) {
                                $query->where('status', '=', $value);
                            }
                        })
                        ->editColumn('firstname', function ($row = null) {
                            return ($row->firstname) ? $row->firstname : '---';
                        })
                        ->editColumn('lastname', function ($row = null) {
                            return ($row->lastname) ? $row->lastname : '---';
                        })
                        ->editColumn('email', $this->getUserEmail($query = null))
                        ->editColumn('created_at', function ($model) {
                            return format_x_days($model->created_at);
                        })
                        ->editColumn('status', function ($model) {
                            return ((int) $model->status) ? '<span class="label-basic label-basic--success">ACTIVE</span>' : '<span class="label-basic label-basic--danger">Archived</span>';
                        })
                        ->addColumn('action', $this->getActionsColumn($canUpdateUser, $canDeleteUser, $canLoginAsUser))
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        view()->share('content_class', 'side-menu');
        $html = app('html');

        return $this->setName('Users List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('antares/foundation::label.users.id')])
                        ->addColumn([
                            'data'      => 'firstname',
                            'name'      => 'firstname',
                            'title'     => trans('antares/foundation::label.users.firstname'),
                            'className' => 'bolded',
                        ])
                        ->addColumn([
                            'data'      => 'lastname',
                            'name'      => 'lastname',
                            'title'     => trans('antares/foundation::label.users.lastname'),
                            'className' => 'bolded',
                        ])
                        ->addColumn([
                            'data'      => 'email',
                            'name'      => 'email',
                            'title'     => trans('antares/foundation::label.users.email'),
                            'className' => 'bolded',
                        ])
                        ->addColumn([
                            'data'  => 'created_at',
                            'name'  => 'created_at',
                            'title' => trans('antares/foundation::label.users.created_at'),
                        ])
                        ->addColumn([
                            'data'  => 'status',
                            'name'  => 'status',
                            'title' => trans('antares/foundation::label.users.status'),
                        ])
                        ->addAction([
                            'name'       => 'edit',
                            'title'      => '',
                            'class'      => 'mass-actions dt-actions',
                            'orderable'  => false,
                            'searchable' => false,
                        ])
                        ->addMassAction('delete', $html->link(handles('antares/foundation::users/delete', ['csrf' => true]), $html->raw('<i class="zmdi zmdi-delete"></i><span>' . trans('Delete') . '</span>'), [
                                    'class'            => "triggerable confirm mass-action",
                                    'data-title'       => trans("Are you sure?"),
                                    'data-description' => trans('Deleting users'),
                        ]))
                        ->setDeferedData()
                        ->addGroupSelect($this->statuses(), 5, 1)
                        ->ajax(handles('antares/foundation::/users/index'));
    }

    /**
     * Creates select for statuses
     *
     * @return String
     */
    protected function statuses()
    {

        $statuses = User::select([DB::raw('count(id) as counter'), 'status'])->clients()->groupBy('status')->get()->lists('counter', 'status')->toArray();
        return ['all' => trans('antares/users::messages.statuses.all'),
            0     => trans('antares/users::messages.statuses.archived', ['count' => array_get($statuses, 0, 0)]),
            1     => trans('antares/users::messages.statuses.active', ['count' => array_get($statuses, 1, 0)])
        ];

//
//
//        $selected = app('request')->ajax() ? null : 1;
//        return Form::select('status', [
//                    'all' => trans('antares/users::messages.statuses.all'),
//                    0     => trans('antares/users::messages.statuses.archived', ['count' => array_get($statuses, 0, 0)]),
//                    1     => trans('antares/users::messages.statuses.active', ['count' => array_get($statuses, 1, 0)]),
//                        ], $selected, [
//                    'data-prefix'            => '',
//                    'data-selectAR--mdl-big' => "true",
//                    'class'                  => 'users-select-status mr24 select2--prefix',
//        ]);
    }

    /**
     * Get actions column for table builder.
     *
     * @return callable
     */
    protected function getActionsColumn($canUpdateUser, $canDeleteUser, $canLoginAsUser)
    {
        return function ($row) use ($canUpdateUser, $canDeleteUser, $canLoginAsUser) {
            $html               = app('html');
            $this->tableActions = [];
            $user               = auth()->user();
            if ($canUpdateUser) {
                $this->addTableAction('show', $row, $html->link(handles("antares::users/{$row->id}"), trans('antares/foundation::label.show'), ['data-icon' => 'eye']));
            }
            if ($canUpdateUser) {
                $this->addTableAction('edit', $row, $html->link(handles("antares::users/{$row->id}/edit"), trans('antares/foundation::label.edit'), ['data-icon' => 'edit']));
            }
            if (!is_null($user) && $user->id !== $row->id && $canDeleteUser) {
                $this->addTableAction('delete', $row, $html->link(handles("antares::users/{$row->id}/delete", ['csrf' => true]), trans('antares/foundation::label.delete'), [
                            'class'            => "triggerable confirm",
                            'data-icon'        => 'delete',
                            'data-title'       => trans("Are you sure?"),
                            'data-description' => trans('Deleteing user') . ' ' . $row->fullname,
                ]));
            }
            if (!is_null($user) && $user->id !== $row->id && $canLoginAsUser) {
                $this->addTableAction('login_as', $row, $html->link(handles("login/with/{$row->id}"), trans('antares/control::label.login_as', ['fullname' => $row->fullname]), [
                            'class'            => 'triggerable confirm',
                            'data-icon'        => 'odnoklassniki',
                            'data-title'       => trans("Are you sure?"),
                            'data-description' => trans('antares/control::label.login_as', ['fullname' => $row->fullname]),
                ]));
            }

            if (empty($this->tableActions)) {
                return '';
            }
            $section = $html->create('div', $html->raw(implode('', $this->tableActions->toArray())), ['class' => 'mass-actions-menu', 'style' => 'display:none;', 'data-id' => $row->id])->get();

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
            return HTML::link('mailto:' . $row->email, $row->email);
        };
    }

}
