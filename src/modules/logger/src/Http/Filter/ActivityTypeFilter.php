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



namespace Antares\Logger\Http\Filter;

use Yajra\Datatables\Contracts\DataTableScopeContract;
use Antares\Datatables\Filter\SelectFilter;
use Antares\Logger\Model\LogTypes;
use Antares\Support\Str;

class ActivityTypeFilter extends SelectFilter implements DataTableScopeContract
{

    /**
     * name of filter
     *
     * @var String 
     */
    protected $name = 'Types';

    /**
     * column to search
     *
     * @var String
     */
    protected $column = 'type';

    /**
     * filter pattern
     *
     * @var String
     */
    protected $pattern = 'Type: %value';

    /**
     * filter instance dataprovider
     * 
     * @return array
     */
    protected function options()
    {
        $types  = app(LogTypes::class)->select(['name', 'id'])->get();
        $return = [];
        foreach ($types as $type) {
            $return = array_add($return, $type->id, ucfirst(Str::humanize($type->name)));
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
        $values = $this->getValues();
        if (empty($values)) {
            return $builder;
        }

        return $builder->whereHas('component', function($query) use($values) {
                    $query->whereIn('id', $values);
                });
    }

}
