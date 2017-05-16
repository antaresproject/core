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
use Antares\Automation\Model\Jobs;
use Antares\Support\Collection;

class AutomationLogsFilter extends SelectFilter implements DataTableScopeContract
{

    /**
     * Name of filter
     *
     * @var String 
     */
    protected $name = 'Script name';

    /**
     * Column to search
     *
     * @var String
     */
    protected $column = 'script_name';

    /**
     * filter pattern
     *
     * @var String
     */
    protected $pattern = '%value';

    /**
     * filter instance dataprovider
     * 
     * @return Collection
     */
    protected function options()
    {
        $jobs   = Jobs::all(['id', 'name', 'value']);
        $return = [];
        foreach ($jobs as $job) {
            $return[$job->id] = $job->value['title'];
        }
        return $return;
    }

    /**
     * filters data by parameters from memory
     * 
     * @param mixed $builder
     */
    public function apply($builder)
    {
        $params = $this->getParams();

        if (is_null($ids = array_get($params, __CLASS__ . '.value'))) {
            return false;
        }
        if (!empty($ids)) {
            $builder->whereIn('job_id', $ids);
        }
    }

}
