<?php

/**
 * Part of the Antares package.
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
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Users\Http\Filter;

use Yajra\Datatables\Contracts\DataTableScopeContract;
use Antares\Datatables\Filter\DateRangeFilter;

class UserCreatedAtFilter extends DateRangeFilter implements DataTableScopeContract
{

    /**
     * Name of filter
     *
     * @var String 
     */
    protected $name = 'Created At';

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
        'row_title' => 'antares/users::messages.created_at_filter'
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
            $start = array_get($range, 'start') . ' 00:00:00';
            $end   = array_get($range, 'end') . ' 23:59:59';
            $builder->whereBetween('tbl_users.created_at', [$start, $end]);
            return $builder;
        }
    }

}
