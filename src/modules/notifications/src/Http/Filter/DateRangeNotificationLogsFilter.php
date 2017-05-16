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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications\Filter;

use Yajra\Datatables\Contracts\DataTableScopeContract;
use Antares\Datatables\Filter\DateRangeFilter;

class DateRangeNotificationLogsFilter extends DateRangeFilter implements DataTableScopeContract
{

    /**
     * Name of filter
     *
     * @var String 
     */
    protected $name = 'Date Range';

    /**
     * Column to search
     *
     * @var String
     */
    protected $column = 'daterange';

    /**
     * Filter attributes
     *
     * @var array
     */
    protected $attributes = [
        'row_title' => 'antares/notifications::logs.filter.daterange'
    ];

    /**
     * Filters data by parameters from memory
     * 
     * @param mixed $builder
     */
    public function apply($builder)
    {
        if (!empty($values = $this->getValues())) {
            $range = json_decode($values, true);
            if (!isset($range['start']) or ! isset($range['end'])) {
                return $builder;
            }
            $start = $range['start'] . ' 00:00:00';
            $end   = $range['end'] . ' 23:59:59';
            $builder->whereBetween('tbl_notifications_stack.created_at', [$start, $end]);
        }
    }

}
