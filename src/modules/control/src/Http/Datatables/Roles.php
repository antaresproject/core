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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Foundation\Http\Datatables;

use Antares\Datatables\Services\DataTable;
use Antares\Support\Facades\Foundation;

class Roles extends DataTable
{

    /**
     * items per page
     *
     * @var mixed 
     */
    public $perPage = 25;

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return Foundation::make('antares.role')->managers()->select(['id', 'full_name', 'description']);
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $roleId        = user()->roles->first()->id;
        $acl           = app('antares.acl')->make('antares/control');
        $canEditRole   = $acl->can('edit-role');
        $canDeleteRole = $acl->can('delete-role');
        return $this->prepare()
                        ->addColumn('action', $this->getActionsColumn($roleId, $canEditRole, $canDeleteRole))
                        ->editColumn('description', function ($model) {
                            return (strlen($model->description) > 0) ? value($model->description) : '---';
                        })
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this->setName('Roles List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Id'])
                        ->addColumn(['data' => 'full_name', 'name' => 'full_name', 'title' => 'Group name', 'className' => 'bolded'])
                        ->addColumn(['data' => 'description', 'name' => 'description', 'title' => 'Description'])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->setDeferedData();
    }

    /**
     * Get actions column for table builder.
     *
     * @return callable
     */

    /**
     * prepare datatable row actions
     * 
     * @param mixed $roleId
     * @param boolean $canEditRole
     * @param boolean $canDeleteRole
     * @return callable
     */
    protected function getActionsColumn($roleId, $canEditRole, $canDeleteRole)
    {

        return function ($row) use($roleId, $canEditRole, $canDeleteRole) {
            $btns = [];
            $html = app('html');
            if ($canEditRole) {
                array_push($btns, $html->create('li', $html->link(handles("antares::control/roles/{$row->id}/edit"), trans('antares/control::title.edit_details'), ['data-icon' => 'edit'])));
                array_push($btns, $html->create('li', $html->link(handles("antares::control/roles/{$row->id}/acl"), trans('antares/control::title.acl_rules'), ['data-icon' => 'accounts-alt'])));
            }
            if ($row->id !== $roleId and $canDeleteRole) {

                $url = handles("antares::control/roles/{$row->id}/delete", ['csrf' => true]);

                $class           = 'triggerable confirm';
                $dataTitle       = trans('Are you sure?');
                $dataDescription = trans('antares/control::global.deleteing_role', ['name' => $row->name]);

                if ($row->users->count()) {
                    publish('control', ['js/delete-group-denied.js']);
                    $url             = '#';
                    $class           = 'bindable delete-group-denied';
                    $dataTitle       = trans('This action is not allowed');
                    $dataDescription = trans('There are users attached to this group. Only empty groups can be removed.');
                }


                array_push($btns, $html->create('li', $html->link($url, trans('Delete'), [
                                    'class'            => $class,
                                    'data-icon'        => 'delete',
                                    'data-title'       => $dataTitle,
                                    'data-description' => $dataDescription
                ])));
            }
            if (empty($btns)) {
                return '';
            }
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}
