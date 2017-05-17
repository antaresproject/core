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

class SettingsMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'       => 'settings',
        'position' => '*',
        'title'    => 'antares/foundation::title.settings.list',
        'link'     => '#',
        'icon'     => 'zmdi-settings',
    ];

    /**
     * Get the title.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function getTitleAttribute($value)
    {
        return $this->container->make('translator')->trans($value);
    }

    /**
     * Get title attribute
     *
     * @return String
     */
    public function getActiveAttribute()
    {
        $segment = request()->segment(2);
        if ($segment == 'control') {
            return true;
        }
        return parent::getActiveAttribute();
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

    /**
     * Create a handler.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->passesAuthorization()) {
            return;
        }
        $menu = $this->createMenu();
        $menu->icon('zmdi-settings')
                ->type('secondary');


        $this->handler
                ->add('general-config', '^:settings')
                ->title('General configuration')
                ->link(handles('antares::settings/index'))
                ->icon('icon--billing');
    }

}
