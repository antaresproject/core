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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Automation\Filter;

use Yajra\Datatables\Contracts\DataTableScopeContract;
use Antares\Datatables\Filter\DateRangeFilter;

class AutomationDateRangeFilter extends DateRangeFilter implements DataTableScopeContract
{

    /**
     * Name of filter
     *
     * @var String 
     */
    protected $name = 'Executed At';

    /**
     * Column to search
     *
     * @var String
     */
    protected $column = 'created_at';

    /**
     * Filter attributes
     *
     * @var array
     */
    protected $attributes = [
        'row_title' => 'antares/automation::messages.executed_at'
    ];

    /**
     * filters data by parameters from memory
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
        }
    }

}
