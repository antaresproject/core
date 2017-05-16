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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Http\Handlers;

use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Authorization\Authorization;

class ErrorLogBreadcrumb extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'error_log',
        'title' => 'Error Log',
        'link'  => 'antares::logger/system/index',
        'icon'  => null,
        'boot'  => [
            'group' => 'menu.top.error_log',
            'on'    => 'antares/logger::admin.index.details'
        ]
    ];

    /**
     * Get the title.
     * 
     * @return string
     */
    public function getTitleAttribute()
    {
        return $this->container->make('translator')->trans('antares/logger::messages.error_log_breadcrumb_details_title', ['date' => from_route('date')]);
    }

    /**
     * Check authorization to display the menu.
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return app('antares.acl')->make('antares/logger')->can('error-details');
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
        $date = from_route('date');

        $this->handler
                ->add('error_log_download', '^:error_log')
                ->title(trans('antares/logger::messages.error_log_download'))
                ->icon('zmdi-download')
                ->link(handles('antares::logger/download/' . $date));

        $this->handler->add('error_log_delete', '^:error_log')
                ->title(trans('antares/logger::messages.delete'))
                ->icon('zmdi-delete')
                ->link(handles('antares::logger/delete/' . $date))
                ->attributes(['class' => 'triggerable confirm', 'data-title' => trans('antares/logger::messages.delete_ask'), 'data-description' => trans('antares/logger::messages.delete_description', ['date' => $date])]);
    }

}
