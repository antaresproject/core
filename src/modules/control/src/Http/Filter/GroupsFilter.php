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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Control\Http\Filter;

use Yajra\Datatables\Contracts\DataTableScopeContract;
use Antares\Datatables\Filter\SelectFilter;
use Antares\Model\Role;

class GroupsFilter extends SelectFilter implements DataTableScopeContract
{

    /**
     * name of filter
     *
     * @var String 
     */
    protected $name = 'Groups';

    /**
     * column to search
     *
     * @var String
     */
    protected $column = 'group';

    /**
     * filter pattern
     *
     * @var String
     */
    protected $pattern = 'Group: %value';

    /**
     * filter instance dataprovider
     * 
     * @return array
     */
    protected function options()
    {
        return app(Role::class)->whereNotIn('name', ['member', 'quest', 'guest'])->get()->pluck('full_name', 'id')->toArray();
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
        $builder->whereHas('roles', function ($query) use($values) {
            $query->whereIn('tbl_roles.id', $values);
        });
    }

}
