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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Brands\Http\Handlers;

use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Auth\Guard;

class BrandsMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'brands',
        'title' => 'Branding',
        'link'  => 'antares::multibrand/index',
        'icon'  => 'zmdi-settings-square',
    ];

    /**
     * Get position.
     *
     * @return string
     */
    public function getPositionAttribute()
    {
        return $this->handler->has('settings.general-config') ? '>:settings.general-config' : '>:home';
    }

    public function getLinkAttribute($value = null)
    {
        if (extension_active('multibrand')) {
            return handles('antares::multibrand/index');
        }
        return handles('antares::brands/' . brand_id() . '/edit');
    }

    /**
     * Check whether the menu should be displayed.
     *
     * @param  Guard  $auth
     *
     * @return bool
     */
    public function authorize(Guard $auth)
    {
        return !$auth->guest();
    }

}
