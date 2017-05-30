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
 * @package    Module
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Foundation\Http\Handlers;

use Antares\Contracts\Authorization\Authorization;
use Antares\Foundation\Support\MenuHandler;

class ComponentsMainMenu extends MenuHandler
{

    /**
     * Configuration
     *
     * @var array
     */
    protected $menu = [
        'id'       => 'components',
        'position' => '>:system.system_informations',
        'link'     => 'antares/foundation::/modules',
        'icon'     => 'zmdi-apps',
    ];

    /**
     * Access control
     * 
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return $acl->can('manage-antares');
    }

    /**
     * Title attribute getter
     * 
     * @return bool
     */
    public function getTitleAttribute()
    {
        return trans('antares/foundation::title.modules_management');
    }

}
