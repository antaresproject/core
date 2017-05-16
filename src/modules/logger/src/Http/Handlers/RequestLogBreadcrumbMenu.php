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

class RequestLogBreadcrumbMenu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'   => 'request_log',
        'link' => 'antares::logger/request/index',
        'icon' => null,
        'boot' => [
            'group' => 'menu.top.request_log',
            'on'    => 'antares/logger::admin.request.index'
        ]
    ];

    /**
     * Get the title.
     * 
     * @return string
     */
    public function getTitleAttribute()
    {
        return from_route('date');
    }

    /**
     * Check authorization to display the menu.
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return app('antares.acl')->make('antares/logger')->can('request-list');
    }

    /**
     * Create a handler.
     * @return void
     */
    public function handle()
    {
        if (!from_route('date')) {
            return;
        }
        if (!$this->passesAuthorization()) {
            return;
        }
        $this->createMenu();
        $date = from_route('date');

        $this->handler
                ->add('request_log_download', '^:request_log')
                ->title(trans('antares/logger::messages.request_log_download'))
                ->icon('zmdi-download')
                ->link(handles('antares::logger/request/download/' . $date));

        $this->handler->add('request_log_delete', '^:request_log')
                ->title(trans('antares/logger::messages.delete'))
                ->icon('zmdi-delete')
                ->link(handles('antares::logger/request/clear/' . $date))
                ->attributes(['class'            => 'triggerable confirm',
                    'data-title'       => trans('antares/logger::messages.request_log_delete_ask'),
                    'data-description' => trans('antares/logger::messages.request_log_delete_description', ['date' => $date])]);
    }

}
