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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
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
        'id'    => 'staff',
        'title' => 'Staff',
        'link'  => 'antares::control/roles/index',
        'icon'  => 'icon-support',
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
     * Get the URL.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getLinkAttribute($value)
    {
        $control = app('antares.acl')->make('antares/control');

        if ($control->can('roles-list')) {
            return handles('antares::control/roles/index');
        }
        if ($control->can('admin-list')) {
            return handles('antares::control/users/index');
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
