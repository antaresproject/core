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



namespace Antares\Automation\Http\Handlers;

use Antares\Contracts\Auth\Guard;
use Antares\Foundation\Support\MenuHandler;

class Menu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'automation',
        'title' => 'Automation',
        'link'  => 'antares::automation/index',
        'icon'  => 'icon-support',
    ];

    /**
     * Get position.
     *
     * @return string
     */
    public function getPositionAttribute()
    {
        return $this->handler->has('settings.ban_management') ? '>:settings.ban_management' : '>:settings.general-config';
    }

    /**
     * Check whether the menu should be displayed.
     *
     * @param  \Antares\Contracts\Auth\Guard  $auth
     *
     * @return bool
     */
    public function authorize(Guard $auth)
    {
        return app('antares.acl')->make('antares/automation')->can('automation-list');
    }

}
