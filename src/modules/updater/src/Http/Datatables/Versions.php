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

class Versions extends DataTable
{

    /**
     * items per page
     *
     * @var mixed 
     */
    public $perPage = 25;

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        return Foundation::make('Antares\Updater\Model\Version')->select(['id', 'app_version', 'description', 'changelog', 'last_update_date', 'is_actual']);
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {

        return $this->prepare()
                        ->editColumn('description', function ($model) {
                            return (strlen($model->description) > 0) ? '<span class="text-wrapped">' . $model->description . '</span>' : '---';
                        })
                        ->editColumn('changelog', function ($model) {
                            return (strlen($model->changelog) > 0) ? '<span class="text-wrapped">' . strip_tags($model->changelog) . '</span>' : '---';
                        })
                        ->editColumn('is_actual', function ($model) {
                            return ((int) $model->is_actual) ? '<span class="label-basic label-basic--success">ACTIVE</span>' : '<span class="label-basic label-basic--default">INACTIVE</span>';
                        })->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this->setName('Versions List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('Id')])
                        ->addColumn(['data' => 'app_version', 'name' => 'app_version', 'title' => trans('System Version')])
                        ->addColumn(['data' => 'description', 'name' => 'description', 'title' => trans('Description')])
                        ->addColumn(['data' => 'changelog', 'name' => 'changelog', 'title' => trans('Change Log')])
                        ->addColumn(['data' => 'last_update_date', 'name' => 'last_update_date', 'title' => trans('Update date')])
                        ->addColumn(['data' => 'is_actual', 'name' => 'is_actual', 'title' => trans('Status')])
                        ->setDeferedData();
    }

}
