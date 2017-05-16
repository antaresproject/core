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
use Illuminate\Support\Facades\Event;

class System extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'       => 'system',
        'position' => '>:logger',
        'title'    => 'System',
        'link'     => '#',
        'icon'     => 'zmdi-settings-square',
        'type'     => 'secondary'
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
        return $acl->can('manage-antares');
    }

    public function handle()
    {
        $id = $this->getAttribute('id');
        Event::fire('antares.ready: menu.before.' . $id);
        if (!$this->passesAuthorization()) {
            return;
        }
        $acl                 = app('antares.acl')->make('antares/logger');
        $canViewInformations = $acl->can('view-logs');
        $menu                = $this->createMenu();

        $this->attachIcon($menu);
        if ($canViewInformations) {
            $this->handler->add('system_informations', '^:' . $id)
                    ->link(handles('antares::logger/information/index'))
                    ->title(trans('antares/logger::global.system_information'));
        }
        Event::fire('antares.ready: menu.after.' . $id);
    }

}
