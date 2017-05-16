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
use Illuminate\Support\Facades\Event;
use Antares\Contracts\Auth\Guard;

class Menu extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'    => 'logger',
        'title' => 'Logs',
        'link'  => 'antares::logger/activity/index',
        'icon'  => 'zmdi-format-list-bulleted',
    ];

    /**
     * Get position.
     *
     * @return string
     */
    public function getPositionAttribute()
    {
        return '>:home';
    }

    /**
     * Get title attribute
     *
     * @return String
     */
    public function getActiveAttribute()
    {
        return $this->isActiveErrorLog();
    }

    protected function isActiveErrorLog()
    {
        $request = request();
        if ($request->segment(2) == 'logger' && in_array($request->segment(3), ['details', 'system'])) {
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
     * Check whether the menu should be displayed.
     *
     * @param  Guard  $auth
     *
     * @return bool
     */
    public function authorize(Guard $auth)
    {
        return app('antares.acl')->make('antares/logger')->can('view-logs');
    }

    /**
     * {@inheritdeoc}
     */
    public function handle()
    {
        $id = $this->getAttribute('id');
        Event::fire('antares.ready: menu.before.' . $id);

        if (!$this->passesAuthorization()) {
            return;
        }
        $acl             = app('antares.acl')->make('antares/logger');
        $canErrorList    = $acl->can('error-list');
        $canActivityList = $acl->can('activity-dashboard');
        $canRequestList  = $acl->can('request-list');

        $menu = $this->createMenu();
        $menu->type('secondary');

        $this->attachIcon($menu);
        if ($canActivityList) {
            $this->handler->add('activity-log', '^:' . $id)
                    ->link(handles('antares::logger/activity/index'))
                    ->title('Activity Log');
        }
        if ($canErrorList) {
            $this->handler->add('error-log', '^:' . $id)
                    ->link(handles('antares::logger/system/index'))
                    ->title(trans('Error Log'))
                    ->active($this->isActiveErrorLog());
        }
        if ($canRequestList) {
            $this->handler->add('request-log', '^:' . $id)
                    ->link(handles('antares::logger/request/index'))
                    ->title(trans('Request Log'));
        }


        Event::fire('antares.ready: menu.after.' . $id);
    }

}
