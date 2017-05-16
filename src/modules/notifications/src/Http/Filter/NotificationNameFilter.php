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
use Antares\Notifications\Model\Notifications;
use Antares\Datatables\Filter\SelectFilter;

class NotificationNameFilter extends SelectFilter implements DataTableScopeContract
{

    /**
     * Name of filter
     *
     * @var String 
     */
    protected $name = 'Notification Name';

    /**
     * Column to search
     *
     * @var String
     */
    protected $column = 'notification_name';

    /**
     * filter pattern
     *
     * @var String
     */
    protected $pattern = 'antares/notifications::logs.filter.names';

    /**
     * Filter instance dataprovider
     * 
     * @return Collection
     */
    protected function options()
    {
        return Notifications::query()->get(['id', 'event'])->pluck('event', 'id')->toArray();
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
            return false;
        }
        $builder->whereIn('tbl_notifications.id', $values);
    }

}
