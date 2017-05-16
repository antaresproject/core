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

use Antares\Datatables\Services\DataTable;
use Antares\Logger\Utilities\LogViewer;
use Antares\Support\Collection;
use function trans;
use function app;

class ErrorLogs extends DataTable
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
        $stats = app(LogViewer::class)->statsTable();
        $rows  = [];
        foreach ($stats->rows() as $row) {
            array_push($rows, $row);
        }
        return new Collection($rows);
    }

    /**
     * get table headers
     * 
     * @return array
     */
    protected function getHeaders()
    {
        $stats = app(LogViewer::class)->statsTable();
        return $stats->header();
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $acl = app('antares.acl')->make('antares/logger');

        $canViewDetails = $acl->can('error-details');
        $canDownload    = $acl->can('error-download');
        $canDelete      = $acl->can('error-delete');

        $rows = $this->prepare()
                        ->editColumn('id', function ($row = null) {
                            $this->counter++;
                            return $this->counter;
                        })->editColumn('date', function ($item) {
            return format_x_days(array_get($item, 'date'));
        });
        $headers = $this->getHeaders();
        foreach ($headers as $name => $header) {
            if ($name === 'date') {
                continue;
            }
            $rows->editColumn($name, function ($item) use($name) {
                return '<span class="level level-' . (array_get($item, $name, 0) !== 0 ? $name : 'empty') . '">
                            ' . array_get($item, $name) . '
                        </span>';
            });
        }
        return $rows->addColumn('action', $this->getActionsColumn($canViewDetails, $canDownload, $canDelete))
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        $headers = $this->getHeaders();
        $builder = $this->setName('Error Log List')
                ->addColumn(['id' => 'id', 'name' => 'id', 'data' => 'id', 'title' => 'Id']);
        foreach ($headers as $header => $title) {

            $title = ($header == 'date') ?
                    '<span class="label label-info">' . $title . '</span>' :
                    '<span class="level level-' . $header . '">' . (log_styler()->icon($header) . ' ' . $header) . '</span>';

            $builder->addColumn(['data' => $header, 'name' => $header, 'title' => $title]);
        }
        return $builder->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->setDeferedData();
    }

    /**
     * Get actions column for table builder.
     * @return callable
     */
    protected function getActionsColumn($canViewDetails, $canDownload, $canDelete)
    {
        return function ($row) use($canViewDetails, $canDownload, $canDelete) {
            $btns = [];
            $html = app('html');
            if ($canViewDetails) {
                $btns[] = $html->create('li', $html->link(handles("antares::logger/details/" . $row['date']), trans('View'), ['data-icon' => 'desktop-windows']));
            }
            if ($canDownload) {
                $btns[] = $html->create('li', $html->link(handles("antares::logger/download/" . $row['date']), trans('Download'), ['data-icon' => 'download']));
            }
            if ($canDelete) {
                $btns[] = $html->create('li', $html->link(handles("antares::logger/delete/" . $row['date']), trans('Delete'), ['class' => "triggerable confirm", 'data-icon' => 'delete', 'data-title' => trans("Are you sure?"), 'data-description' => trans('Deleteing item') . ' #' . $row['date']]));
            }
            if (empty($btns)) {
                return '';
            }
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();

            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}
