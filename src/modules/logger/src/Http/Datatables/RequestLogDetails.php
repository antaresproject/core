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

use Antares\Logger\Entities\RequestLogEntryCollection;
use Antares\Datatables\Services\DataTable;
use Illuminate\Support\Arr;
use Antares\Support\Str;

class RequestLogDetails extends DataTable
{

    /**
     * internal datatable counter
     *
     * @var mixed
     */
    protected $counter = 0;

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $raw                = app('logger.filesystem')->read(from_route('date'));
        $logEntryCollection = new RequestLogEntryCollection();
        $collection         = $logEntryCollection->load($raw);
        $index              = 1;
        $collection->each(function ($item) use(&$index) {
            $item->id = (int) $index;
            ++$index;
        });
        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {

        $query   = app('request')->ajax() ? $this->query() : $this->query()->forPage(1, 25);
        $request = app('request');
        return $this->prepare($query)
                        ->order(function($query) {
                            foreach ($query->request->orderableColumns() as $orderable) {
                                $column = $query->getColumnName($orderable['column']);
                                if ($column === 'id') {
                                    $query->collection = $query->collection->sortBy($column);
                                } else {
                                    $query->collection = $query->collection->sortBy(
                                            function ($row) use ($column, $query) {
                                        $data = $query->serialize($row);
                                        return Arr::get($data, $column);
                                    });
                                }
                                if ($orderable['direction'] == 'desc') {
                                    $query->collection = $query->collection->reverse();
                                }
                            }
                        })
                        ->filter(function ($instance) use ($request) {
                            $search = $request->get('search');
                            $value  = array_get($search, 'value');
                            if (strlen($value) <= 0) {
                                return;
                            }
                            $instance->collection = $instance->collection->filter(function($row) use($value) {
                                return Str::contains($row->header, $value) or
                                        Str::contains($row->level, $value) or
                                        Str::contains($row->env, $value) or
                                        Str::contains($row->datetime->toDateTimeString(), $value);
                            });
                        })
                        ->editColumn('id', function ($row = null) {
                            return $row->id;
                        })
                        ->editColumn('env', function ($row = null) {
                            return $row->env;
                        })->editColumn('datetime', function ($row) {
                    return $row->datetime;
                })->editColumn('content', function ($row) {
                    return $row->header;
                })->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {


        return $this->setName('Request Log Details')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('Id')])
                        ->addColumn(['data' => 'env', 'name' => 'env', 'title' => trans('Environment')])
                        ->addColumn(['data' => 'datetime', 'name' => 'datetime', 'title' => trans('Date')])
                        ->addColumn(['data' => 'content', 'name' => 'content', 'title' => trans('Content')])
                        ->setDeferedData($this->ajax(), $this->query()->count())
                        ->parameters(['iDisplayLength' => $this->perPage]);
    }

}
