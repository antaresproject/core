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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications\Http\Presenters;

use DaveJamesMiller\Breadcrumbs\Facade as Breadcrumbs;
use Illuminate\Database\Eloquent\Model;

class Breadcrumb
{

    /**
     * on list notifications
     */
    protected function onList()
    {
        Breadcrumbs::register('notifications', function($breadcrumbs) {
            $breadcrumbs->push(trans('antares/notifications::messages.notification_templates'), handles('antares::notifications/index'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('notifications'));
    }

    /**
     * when shows notifications list
     * 
     * @param type $type
     */
    public function onTable($type = null)
    {
        if (!is_null($type)) {
            Breadcrumbs::register('notifications-' . $type, function($breadcrumbs) use($type) {
                $breadcrumbs->push('Notifications ' . ucfirst($type), handles('antares::notifications/index'));
            });
            view()->share('breadcrumbs', Breadcrumbs::render('notifications-' . $type));
        }
    }

    /**
     * when shows edit notification form
     * 
     * @param Model $eloquent
     */
    public function onEdit(Model $eloquent)
    {
        $this->onList();
        $title  = null;
        $langId = lang_id();
        foreach ($eloquent->contents as $content) {
            if ($langId == $content->lang_id) {
                $title = $content->title;
            }
        }
        Breadcrumbs::register('notification-edit', function($breadcrumbs) use($title) {
            $breadcrumbs->parent('notifications');
            $breadcrumbs->push(trans('antares/notifications::messages.notification_templates_edit', ['name' => $title]));
        });

        view()->share('breadcrumbs', Breadcrumbs::render('notification-edit'));
    }

    /**
     * when shows create new notification form
     * 
     * @param String $type
     */
    public function onCreate($type = null)
    {
        $this->onList();
        Breadcrumbs::register('notification-create', function($breadcrumbs) {
            $breadcrumbs->parent('notifications');
            $breadcrumbs->push(trans('antares/notifications::messages.notification_templates_create'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('notification-create'));
    }

    /**
     * On notification logs list
     */
    public function onLogsList()
    {
        Breadcrumbs::register('notifications-logs', function($breadcrumbs) {
            $breadcrumbs->push(trans('antares/notifications::logs.notification_log'), handles('antares::notifications/logs/index'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('notifications-logs'));
    }

}
