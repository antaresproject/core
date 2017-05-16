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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Http\Handlers;

use Antares\Contracts\Auth\Guard;
use Antares\Foundation\Support\MenuHandler;

class TesterMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'module-tester',
        'title' => 'Module Tester',
        'link'  => 'antares::tools/tester',
        'icon'  => 'zmdi-check-circle'
    ];

    /**
     * Check whether the menu should be displayed.
     * 
     * @param  \Antares\Contracts\Auth\Guard  $auth
     * @return bool
     */
    public function authorize(Guard $auth)
    {
        return $this->container->make('antares.acl')->make('antares/tester')->can('tools-tester');
    }

    /**
     * Get position.
     *
     * @return string
     */
    public function getPositionAttribute()
    {
        return $this->handler->has('tools.translations') ? '>:tools.translations' : '>:dashboard';
    }

}
