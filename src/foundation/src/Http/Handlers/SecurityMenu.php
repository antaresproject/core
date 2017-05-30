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

namespace Antares\Foundation\src\Http\Handlers;

use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Authorization\Authorization;

class SecurityMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'       => 'security',
        'position' => '*',
        'title'    => 'Security',
        'link'     => 'antares/foundation::settings/security',
        'icon'     => 'zmdi-shield-security',
    ];

    /**
     * Check authorization to display the menu.
     *
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     *
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        $active = (extension_active('ban_management') or extension_active('two_factor_auth'));

        if (!$active) {
            return false;
        }
        return $acl->can('manage-antares');
    }

}
