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

namespace Antares\Notifications;

use Antares\Notifications\Model\NotificationContents;
use Antares\Logger\Model\Logs;

class Contents
{

    /**
     * Notification contens container
     *
     * @var NotificationContents 
     */
    protected $notifications;

    /**
     * name of repositroy model
     * 
     * @return Logs
     */
    public function __construct()
    {
        $this->notifications = app(NotificationContents::class)
                ->select([
                    'tbl_notification_contents.id',
                    'tbl_languages.code',
                    'tbl_notification_contents.title',
                    'tbl_notification_contents.content'
                ])
                ->leftJoin('tbl_notifications', 'tbl_notification_contents.notification_id', '=', 'tbl_notifications.id')
                ->leftJoin('tbl_languages', 'tbl_notification_contents.lang_id', '=', 'tbl_languages.id')
                ->leftJoin('tbl_notification_types', 'tbl_notifications.type_id', '=', 'tbl_notification_types.id')
                ->leftJoin('tbl_notifications_stack', 'tbl_notifications.id', '=', 'tbl_notifications_stack.notification_id')
                ->where('tbl_notification_types.name', 'admin')
                ->whereNotNull('tbl_notification_contents.content')
                ->where('tbl_notification_contents.content', '<>', '')
                ->get();
    }

    /**
     * Finds notification content by title and locale
     * 
     * @param type $operation
     * @param type $locale
     * @return type
     */
    public function find($operation, $locale)
    {
        $model = $this->notifications->first(function ($value, $key) use($operation, $locale) {
            return $value->code == $locale && ($value->title == $operation or $value->name == $operation);
        });
        return !is_null($model) ? $model->content : false;
    }

}
