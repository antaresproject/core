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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\Http\Handlers;

use Antares\Customfields\Model\FieldCategory;
use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Auth\Guard;

class FieldsMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'customfields',
        'title' => 'Custom Fields',
        'link'  => 'antares::customfields/index',
        'icon'  => 'zmdi-format-color-text',
    ];

    /**
     * Get position
     * 
     * @return string
     */
    public function getPositionAttribute()
    {
        return $this->handler->has('settings.notifications') ? '>:settings.notifications' : '>:settings.general-config';
    }

    /**
     * Get link
     * 
     * @return string
     */
    public function getLinkAttribute()
    {
        $category = FieldCategory::query()->first();
        if (is_null($category)) {
            return handles('antares::customfields/index');
        }
        return handles('antares::customfields/' . $category->name . '/index');
    }

    /**
     * Check whether the menu should be displayed.
     * @param  \Antares\Contracts\Auth\Guard  $auth
     * @return bool
     */
    public function authorize(Guard $auth)
    {
        return app('antares.acl')->make('antares/customfields')->can('list-customfields');
    }

}
