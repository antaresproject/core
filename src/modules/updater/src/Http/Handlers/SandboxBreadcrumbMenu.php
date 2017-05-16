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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Logger\Http\Handlers;

use Antares\Contracts\Authorization\Authorization;
use Antares\Foundation\Support\MenuHandler;

class SandboxBreadcrumbMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'   => 'updater-sandbox',
        'link' => 'antares::updater/sandboxes',
        'icon' => null,
        'boot' => [
            'group' => 'menu.top.sandboxes',
            'on'    => 'antares/updater::admin.sandbox.index'
        ]
    ];

    /**
     * Get the title.
     * @param  string  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        return trans('antares/updater::messages.breadcrumb.sandboxes');
    }

    /**
     * Check authorization to display the menu.
     * 
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return app('antares.acl')->make('antares/updater')->can('sandbox-dashboard');
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
        $demo       = env('APP_DEMO');
        $class      = $demo ? 'sandbox-disabled' : '';
        $attributes = [
            'class'            => 'triggerable confirm create-sandbox ' . $class,
            'data-title'       => trans('antares/updater::messages.create_sandbox_ask'),
            'data-description' => trans('antares/updater::messages.creating_sandbox_description'),
        ];
        if ($demo) {
            array_set('data-disabled', trans('antares/updater::messages.creating_sandbox_disabled_for_demo'));
        }
        $this->handler
                ->add('create-sandbox', '^:updater-sandbox')
                ->title(trans('antares/updater::messages.breadcrumb.create_sandbox'))
                ->icon('zmdi-plus-circle-o')
                ->link('#')
                ->attributes($attributes);
    }

}
