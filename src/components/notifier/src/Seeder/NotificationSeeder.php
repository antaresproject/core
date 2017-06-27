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

namespace Antares\Notifier\Seeder;

use Antares\Notifications\Model\NotificationCategory;
use Antares\Notifications\Model\NotificationSeverity;
use Antares\Notifications\Model\NotificationTypes;
use Antares\Notifications\Model\Notifications;
use Antares\Translations\Models\Languages;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{

    public function run()
    {
        ;
    }

    /**
     * Adds notification
     * 
     * @param array $params
     */
    protected function addNotification(array $params = [])
    {
        if (isset($params['type'])) {
            return $this->save($params);
        }

        $areas = $this->getAreas();
        foreach ($areas as $area) {
            $params['type'] = $area;
            $this->save($params);
        }
        return;
    }

    /**
     * Process save notification
     * 
     * @param array $params
     */
    protected function save(array $params = [])
    {
        $contents = array_get($params, 'contents', []);

        $notificationId = $this->insertNotification($params);

        foreach ($contents as $locale => $content) {
            if (!$this->validateLocale($locale)) {
                Log::emergency(sprintf('Invalid locale provided in notification migration script %s in line %s', __FILE__, __LINE__));
                continue;
            }
            $this->insertNotificationContent($notificationId, $locale, $content);
        }
    }

    /**
     * Get application areas
     * 
     * @return array
     */
    private function getAreas()
    {
        return array_merge(array_keys(config('areas.areas')), [config('antares/foundation::handles')]);
    }

    /**
     * Inserts notification content
     * 
     * @param mixes $notificationId
     * @param String $locale
     * @param String $level
     * @param array $content
     * @return boolean
     */
    private function insertNotificationContent($notificationId, $locale, array $content = [])
    {
        return DB::table('tbl_notification_contents')->insert([
                    'notification_id' => $notificationId,
                    'lang_id'         => lang_id($locale),
                    'title'           => array_get($content, 'title'),
                    'subject'         => array_get($content, 'subject'),
                    'content'         => array_get($content, 'content'),
        ]);
    }

    /**
     * Inserts notification
     * 
     * @param array $params
     * @return mixed
     */
    private function insertNotification(array $params = [])
    {
        DB::table('tbl_notifications')->insert([
            'severity_id' => $this->resolveSeverityId($params),
            'category_id' => $this->resolveCategoryId($params),
            'type_id'     => $this->resolveTypeId($params),
            'event'       => array_get($params, 'event', null),
            'active'      => array_get($params, 'active', 1),
        ]);
        return DB::getPdo()->lastInsertId();
    }

    /**
     * Resolves notification category identifier
     * 
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    private function resolveCategoryId(array $params = [])
    {
        $category = array_get($params, 'category', 'default');
        $model    = NotificationCategory::where('name', $category)->firstOrFail();
        return $model->id;
    }

    /**
     * Resolves notification type identifier
     * 
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    private function resolveTypeId(array $params = [])
    {
        $type = array_get($params, 'type', 'admin');
        try {
            return NotificationTypes::where('name', $type)->firstOrFail()->id;
        } catch (Exception $ex) {
            Log::error($ex);
            throw $ex;
        }
    }

    /**
     * Resolves notification severity identifier
     * 
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    private function resolveSeverityId(array $params = [])
    {
        $severity = array_get($params, 'severity', 'medium');
        $model    = NotificationSeverity::where('name', $severity)->firstOrFail();
        return $model->id;
    }

    /**
     * Validates locale
     * 
     * @param String $locale
     * @return boolean
     */
    private function validateLocale($locale)
    {
        $language = Languages::where('code', $locale)->first();
        return !is_null($language);
    }

    /**
     * Deletes notification by event name
     * 
     * @param mixed $name
     * @return boolean
     */
    public function deleteNotificationByEventName($name)
    {
        return (is_array($name)) ? Notifications::whereIn('event', $name)->delete() : Notifications::where('event', $name)->delete();
    }

}
