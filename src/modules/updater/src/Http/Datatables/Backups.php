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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Updater\Http\Datatables;

use Antares\Datatables\Services\DataTable;
use Antares\Support\Facades\Foundation;
use Antares\Support\Str;

class Backups extends DataTable
{

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        return Foundation::make('Antares\Updater\Model\Backup')->select(['id', 'name', 'version', 'status', 'created_at']);
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $canRestore = app('antares.acl')->make('antares/updater')->can('restore-backup');
        return $this->prepare()
                        ->editColumn('name', function ($model) {
                            return !strlen($model->name) ? '---' : $model->name;
                        })
                        ->editColumn('status', function ($model) {
                            $status = is_null($model->status) ? 'danger' : (in_array($model->status, ['completed']) ? 'success' : 'pending');

                            return '<span class="label-basic label-basic--' . $status . '">' . ucfirst(Str::humanize($model->status)) . '</span>';
                        })
                        ->addColumn('action', $this->getActionsColumn($canRestore))->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this->setName('Backups List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('Id')])
                        ->addColumn(['data' => 'name', 'name' => 'name', 'title' => trans('Name')])
                        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => trans('Status')])
                        ->addColumn(['data' => 'version', 'name' => 'version', 'title' => trans('Version')])
                        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => trans('Created date')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->setDeferedData();
    }

    /**
     * Get actions column for table builder.
     * @return callable
     */
    protected function getActionsColumn($canRestore)
    {
        return function ($row) use($canRestore) {
            if ((int) $row->is_actual or $row->status === 'in_progress') {
                return '';
            }
            $btns = [];
            $html = app('html');
            if ($row->status == 'pending') {
                $btns[] = $html->create('li', $html->link(handles("antares::updater/backups/delete/{$row->id}", ['csrf' => true]), trans('antares/updater::messages.delete_backup_queue'), [
                            'class'            => "triggerable delete-backup-queue",
                            'data-icon'        => 'delete',
                            'data-title'       => trans("antares/updater::messages.are_you_sure_to_delete_queue"),
                            'data-description' => trans('antares/updater::messages.deleteing_backup_queue')]));
            }
            if ($canRestore and $row->status === 'completed') {
                $btns[] = $html->create('li', $html->link(handles("antares::updater/backups/restore/{$row->id}", ['csrf' => true]), trans('antares/updater::messages.restore'), ['class'            => "triggerable backup", 'data-icon'        => 'time-restore-setting',
                            'data-title'       => trans("antares/updater::messages.are_you_sure_to_restore"),
                            'data-description' => trans('antares/updater::messages.restoring_application', ['name' => $row->name])]));
            }
            if (empty($canRestore)) {
                return '';
            }
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}
