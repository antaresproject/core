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

use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Authorization\Authorization;

class FieldsBreadcrumbMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'customfields',
        'title' => 'Custom Fields',
        'link'  => 'antares::customfields',
        'icon'  => null,
        'boot'  => [
            'group' => 'menu.top.customfields',
            'on'    => 'antares/customfields::admin.list'
        ]
    ];

    /**
     * Get the title.
     * @param  string  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        return $this->container->make('translator')->trans($value);
    }

    /**
     * Check authorization to display the menu.
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return app('antares.acl')->make('antares/customfields')->can('list-customfields');
    }

    /**
     * Create a handler.
     * @return void
     */
    public function handle()
    {
        if (!$this->passesAuthorization()) {
            return;
        }
        $this->createMenu();
        if (!app('antares.acl')->make('antares/customfields')->can('add-customfield')) {
            return;
        }
        $this->handler
                ->add('customfields-add', '^:customfields')
                ->title('Add Custom Field')
                ->icon('zmdi-plus-circle')
                ->link(handles('antares::customfields/create'));
    }

}
