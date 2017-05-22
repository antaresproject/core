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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Foundation\Http\Handlers;

use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Authorization\Authorization;

class StaffMenuHandler extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'   => 'staff',
        'link' => 'antares::control/roles/index',
        'icon' => 'icon-support',
    ];

    /**
     * Get position.
     *
     * @return string
     */
    public function getPositionAttribute()
    {
        return $this->handler->has('dashboard') ? '^:settings' : '>:home';
    }

    /**
     * Get title attribute
     *
     * @return String
     */
    public function getTitleAttribute()
    {
        return trans('antares/foundation::title.staff');
    }

    /**
     * Get title attribute
     *
     * @return String
     */
    public function getActiveAttribute()
    {
        return request()->segment(2) === 'control';
    }

    /**
     * Get title attribute
     *
     * @return String
     */
    public function getTypeAttribute()
    {
        return 'secondary';
    }

    /**
     * Get the URL.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getLinkAttribute($value)
    {
        $control = app('antares.acl')->make('antares/acl');

        if ($control->can('roles-list')) {
            return handles('antares::acl/index/roles');
        }
        if ($control->can('admin-list')) {
            return handles('antares::acl/users/index');
        }

        return '#';
    }

    /**
     * Check authorization to display the menu.
     *
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     *
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return $acl->can('manage-antares');
    }

}
