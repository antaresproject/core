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

use Antares\Logger\Entities\RequestLogCollection;
use Antares\Datatables\Services\DataTable;
use Antares\Support\Str;

class RequestLogs extends DataTable
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
        $collection = new RequestLogCollection();
        $return     = [];
        $counter    = 0;

        foreach ($collection as $item) {
            ++$counter;
            $return[] = [
                'date'           => $item->date,
                'id'             => $counter,
                'filename'       => $item->file()->getFilename(),
                'size'           => $item->size(),
                'requests_count' => $item->entries()->count(),
                'created_at'     => $item->createdAt(),
                'updated_at'     => $item->updatedAt()
            ];
        }
        return collect($return);
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $acl             = app('antares.acl')->make('antares/logger');
        $canRequestShow  = $acl->can('request-show');
        $canRequestClear = $acl->can('request-clear');
        $request         = app('request');
        return $this->prepare()
                        ->filter(function ($instance) use ($request) {
                            $search = $request->get('search');
                            $value  = array_get($search, 'value');
                            if (strlen($value) <= 0) {
                                return;
                            }
                            $instance->collection = $instance->collection->filter(function($row) use($value) {
                                return Str::contains($row['id'], $value) or
                                        Str::contains($row['filename'], $value) or
                                        Str::contains($row['size'], $value) or
                                        Str::contains($row['requests_count'], $value) or
                                        Str::contains($row['created_at'], $value) or
                                        Str::contains($row['updated_at'], $value);
                            });
                        })
                        ->editColumn('filename', function ($row) {
                            return $row['filename'];
                        })->editColumn('size', function ($row) {
                            return $row['size'];
                        })->editColumn('requests_count', function ($row) {
                            return $row['requests_count'];
                        })->editColumn('created_at', function ($row) {
                            return format_x_days(str_replace(['http-', '.log'], '', $row['filename']));
                        })->editColumn('updated_at', function ($row) {
                            return format_x_days($row['updated_at']);
                        })
                        ->addColumn('action', $this->getActionsColumn($canRequestShow, $canRequestClear))
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this->setName('Request Log List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('Id'), 'orderable' => false, 'searchable' => false])
                        ->addColumn(['data' => 'filename', 'name' => 'filename', 'title' => trans('Filename')])
                        ->addColumn(['data' => 'size', 'name' => 'size', 'title' => trans('File size')])
                        ->addColumn(['data' => 'requests_count', 'name' => 'requests_count', 'title' => trans('Requests count')])
                        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => trans('Created at')])
                        ->addColumn(['data' => 'updated_at', 'name' => 'updated_at', 'title' => trans('Updated at')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->setDeferedData();
    }

    /**
     * Get actions column for table builder.
     * @return callable
     */
    protected function getActionsColumn($canRequestShow, $canRequestClear)
    {
        return function ($row) use($canRequestShow, $canRequestClear) {
            $btns = [];
            $html = app('html');
            if ($canRequestShow) {
                $btns[] = $html->create('li', $html->link(handles('antares::logger/request/show/' . $row['date']), trans('antares/logger::messages.show_details'), ['data-icon' => 'desktop-windows']));
            }
            if ($canRequestClear) {
                $btns[] = $html->create('li', $html->link(handles('antares::logger/request/clear/' . $row['date']), trans('antares/logger::messages.delete'), [
                            'class'            => "triggerable confirm",
                            'data-icon'        => 'delete',
                            'data-title'       => trans('antares/logger::messages.request_log_delete_ask'),
                            'data-description' => trans('antares/logger::messages.request_log_delete_description', ['date' => $row['date']])]));

                $btns[] = $html->create('li', $html->link(handles('antares::logger/request/download/' . $row['date']), trans('antares/logger::messages.request_log_download'), ['data-icon' => 'download']));
            }

            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}
