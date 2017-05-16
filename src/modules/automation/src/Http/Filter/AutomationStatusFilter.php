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
use Antares\Datatables\Filter\SelectFilter;

class AutomationStatusFilter extends SelectFilter implements DataTableScopeContract
{

    /**
     * name of filter
     *
     * @var String 
     */
    protected $name = 'Status';

    /**
     * column to search
     *
     * @var String
     */
    protected $column = 'status';

    /**
     * filter pattern
     *
     * @var String
     */
    protected $pattern = 'Status: %value';

    /**
     * filter instance dataprovider
     * 
     * @return array
     */
    protected function options()
    {
        return [
            '0' => trans('antares/automation::messages.disabled'),
            '1' => trans('antares/automation::messages.enabled')
        ];
    }

    /**
     * renders filter
     * 
     * @return String
     */
    public function render()
    {
        publish('automation', ['js/automation_status_filter.js']);
        $selected = $this->getValues();
        return view('datatables-helpers::partials._filter_select_multiple', [
                    'options'     => $this->options(),
                    'column'      => $this->column,
                    'placeholder' => trans('antares/automation::messages.select_status_placeholder'),
                    'selected'    => $selected
                ])->render();
    }

    /**
     * filters data by parameters from memory
     * 
     * @param mixed $builder
     */
    public function apply($builder)
    {
        if (!empty($values = $this->getValues())) {
            $builder->whereIn('active', $values);
        }
        return $builder;
    }

}
