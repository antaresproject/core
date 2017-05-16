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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Http\Datatables;

use Antares\Logger\Model\LogsLoginDevices;
use Antares\Datatables\Services\DataTable;
use Antares\Support\Collection;
use Antares\Support\Str;

class Devices extends DataTable
{

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $userId = auth()->user()->id;

        $query = LogsLoginDevices::query()->getQuery();
        $query->from('tbl_logs_login_devices as tlld')
                ->select(['tlld.id', 'tlld.name', 'tlld.machine', 'tlld.browser', 'tlld.system', 'tlld.location', 'tlld.ip_address', 'tlld.updated_at'])
                ->where('user_id', $userId);

        if (!app('request')->ajax()) {
            $query->orderBy('tlld.updated_at', 'desc');
        }
        return new Collection($query->get());
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        return $this->prepare()
                        ->filter(function ($instance) {
                            $search = app('request')->get('search');
                            $value  = array_get($search, 'value');
                            if (strlen($value) <= 0) {
                                return;
                            }
                            $instance->collection = $instance->collection->filter(function($row) use($value) {


                                $value   = mb_strtolower($value);
                                $machine = mb_strtolower($row->machine);
                                $browser = mb_strtolower($row->browser);
                                $name    = mb_strtolower($row->name);

                                $system     = mb_strtolower($row->system);
                                $ip_address = mb_strtolower($row->ip_address);
                                $updated_at = mb_strtolower($row->updated_at);

                                $return = Str::contains($name, $value) or
                                        Str::contains($machine, $value) or
                                        Str::contains($browser, $value) or
                                        Str::contains($system, $value) or
                                        Str::contains($ip_address, $value) or
                                        Str::contains($updated_at, $value);

                                $decoded      = json_decode($row->location, true);
                                $containsCity = false;

                                if (!is_null($city = array_get($decoded, 'city'))) {
                                    $city         = mb_strtolower($city);
                                    $containsCity = Str::contains($city, $value);
                                }
                                $containsCountry = false;

                                if (!is_null($country = array_get($decoded, 'country_name'))) {
                                    $country         = mb_strtolower($country);
                                    $containsCountry = Str::contains($country, $value);
                                }
                                if ($containsCountry) {
                                    return true;
                                }
                                if ($containsCity) {
                                    return true;
                                }
                                return Str::contains($name, $value) or
                                        Str::contains($machine, $value) or
                                        Str::contains($browser, $value) or
                                        Str::contains($system, $value) or
                                        Str::contains($ip_address, $value) or
                                        Str::contains($updated_at, $value);
                            });
                        })
                        ->editColumn('name', function ($model) {
                            return strlen($model->name) > 0 ? $model->name : $model->machine;
                        })
                        ->editColumn('browser', function ($model) {
                            return strlen($model->browser) > 0 ? $model->browser . ', ' . $model->system : '---';
                        })
                        ->editColumn('location', function ($model) {
                            $location = (array) json_decode($model->location, true);

                            $only   = array_filter(array_only($location, ['country_code', 'country_name', 'region_code', 'region_name', 'city', 'zip_code']));
                            $return = implode(', ', $only);
                            return strlen($return) > 0 ? $return : '---';
                        })
                        ->editColumn('updated_at', function ($model) {
                            return format_x_days($model->updated_at);
                        })
                        ->editColumn('ip_address', function ($model) {
                            return strlen($model->ip_address) > 0 ? $model->ip_address : '---';
                        })
                        ->addColumn('action', $this->getActionsColumn())
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this->setName('Devices List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('antares/logger::datagrid.devices.header.id')])
                        ->addColumn(['data' => 'name', 'name' => 'name', 'title' => trans('antares/logger::datagrid.devices.header.name')])
                        ->addColumn(['data' => 'browser', 'name' => 'browser', 'title' => trans('antares/logger::datagrid.devices.header.browser')])
                        ->addColumn(['data' => 'location', 'name' => 'location', 'title' => trans('antares/logger::datagrid.devices.header.location')])
                        ->addColumn(['data' => 'updated_at', 'name' => 'updated_at', 'title' => trans('antares/logger::datagrid.devices.header.last_activity')])
                        ->addColumn(['data' => 'ip_address', 'name' => 'ip_address', 'title' => trans('antares/logger::datagrid.devices.header.last_ip')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions'])
                        ->setDeferedData();
    }

    /**
     * Get actions column for table builder.
     * 
     * @return callable
     */
    protected function getActionsColumn()
    {
        return function ($row) {
            $btns   = [];
            $html   = app('html');
            $btns[] = $html->create('li', $html->link(handles("antares::logger/devices/{$row->id}/edit"), trans('antares/logger::global.devices.edit'), ['data-icon' => 'edit']));
            $btns[] = $html->create('li', $html->link(handles("antares::logger/devices/{$row->id}/delete", ['csrf' => true]), trans('antares/logger::global.devices.delete'), ['data-icon' => 'delete', 'class' => "triggerable confirm", 'data-title' => trans("Are you sure?"), 'data-description' => trans('Deleteing device') . ' ' . $row->name]));

            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
            return '';
        };
    }

}
