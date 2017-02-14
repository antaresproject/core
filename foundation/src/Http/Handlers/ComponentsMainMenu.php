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
 * @package    Module
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
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
        'position' => '>:module',
        'title'    => 'Components',
        'link'     => 'antares/foundation::/extensions',
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

}
