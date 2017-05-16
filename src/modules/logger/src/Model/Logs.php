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

namespace Antares\Logger\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Antares\Notifications\Model\NotificationsStack;
use Antares\Logger\Utilities\LogDecorator;
use Antares\Automation\Model\JobResults;
use Antares\Model\Eloquent;
use Config;

class Logs extends Eloquent
{

    /**
     * @var string
     */
    public $table = 'tbl_logs';

    /**
     * Cast values.
     *
     * @var array
     */
    protected $casts = ['old_value' => 'json', 'new_value' => 'json', 'related_data' => 'json', 'additional_params' => 'json'];

    /**
     * Added attribute.
     *
     * @var array
     */
    protected $appends = ['custom_message', 'custom_fields', 'elapsed_time'];

    /**
     * Fillable columns definition
     *
     * @var array 
     */
    protected $fillable = [
        'type_id',
        'brand_id',
        'user_id',
        'client_id',
        'author_id',
        'priority_id',
        'owner_type',
        'owner_id',
        'old_value',
        'new_value',
        'related_data',
        'additional_params',
        'type',
        'name',
        'route',
        'ip_address',
        'user_agent',
        'is_api_request'
    ];

    /**
     * Get model auditing.
     *
     * @return array revision history
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * Author responsible for the change.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Config::get('auth.providers.users.model'));
    }

    /**
     * Returns data of model.
     *
     * @return object|false
     */
    public function restore()
    {
        if (class_exists($class = $this->owner_type)) {
            $model = $this->$class->findOrFail($this->owner_id);
            $model->fill($this->old_value);

            return $model->save();
        }

        return false;
    }

    /**
     * Get old value.
     *
     * @return mixed
     */
    public function getOldAttribute()
    {
        return $this->old_value;
    }

    /**
     * Get new value.
     *
     * @return mixed
     */
    public function getNewAttribute()
    {
        return $this->new_value;
    }

    /**
     * Get elapsed time.
     * 
     * @return mixed
     */
    public function getElapsedTimeAttribute()
    {
        return is_null($this->created_at) ? '---' : $this->created_at->diffForHumans();
    }

    /**
     * Custom output message.
     *
     * @return mixed
     */
    public function getCustomMessageAttribute()
    {
        if (class_exists($class = $this->owner_type)) {
            return $this->resolveCustomMessage($this->getCustomMessage($class));
        } else {
            return false;
        }
    }

    /**
     * Custom output fields.
     *
     * @return array
     */
    public function getCustomFieldsAttribute()
    {
        if (class_exists($class = $this->owner_type)) {
            $customFields = [];
            foreach ($this->getCustomFields($class) as $field => $message) {
                if (is_array($message) && isset($message[$this->type])) {
                    $customFields[$field] = $this->resolveCustomMessage($message[$this->type]);
                } elseif (is_string($message)) {
                    $customFields[$field] = $this->resolveCustomMessage($message);
                }
            }

            return array_filter($customFields);
        } else {
            return false;
        }
    }

    /**
     * Get custom message.
     *
     * @return string
     */
    public function getCustomMessage($class)
    {
        if (!isset($class::$logCustomMessage)) {
            return 'Not defined custom message!';
        }

        return $class::$logCustomMessage;
    }

    /**
     * Get custom fields.
     *
     * @return string
     */
    public function getCustomFields($class)
    {
        if (!isset($class::$logCustomFields)) {
            return [];
        }

        return $class::$logCustomFields;
    }

    /**
     * Resolve custom message.
     *
     * @param $message
     *
     * @return mixed
     */
    public function resolveCustomMessage($message)
    {
        preg_match_all('/\{[\w.| ]+\}/', $message, $segments);
        foreach (current($segments) as $segment) {
            $s    = str_replace(['{', '}'], '', $segment);
            $keys = explode('|', $s);

            if (empty($keys[1]) && isset($keys[2])) {
                $keys[1] = $this->callback($keys[2]);
            }

            $valueSegmented = $this->getValueSegmented($this, $keys[0], isset($keys[1]) ? $keys[1] : false);
            if (!$valueSegmented) {
                return false;
            }
            $message = str_replace($segment, $valueSegmented, $message);
        }

        return $message;
    }

    /**
     * Message callback.
     *
     * @param $method
     * @return mixed
     */
    public function callback($method)
    {
        if (method_exists($this->owner, $method)) {
            return $this->owner->{$method}($this);
        }

        return;
    }

    /**
     * Get Value of segment.
     *
     * @param $object
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    public function getValueSegmented($object, $key, $default)
    {
        if (is_null($key) || trim($key) == '') {
            return $default;
        }

        foreach (explode('.', $key) as $segment) {
            $object = is_array($object) ? (object) $object : $object;
            if (!isset($object->{$segment})) {
                return $default;
            }

            $object = $object->{$segment};
        }

        return $object;
    }

    /**
     * relation to LogTypes model
     * 
     * @return BelongsTo
     */
    public function component()
    {
        return $this->belongsTo('Antares\Logger\Model\LogTypes', 'type_id', 'id');
    }

    /**
     * relation to LogPriorities model
     * 
     * @return BelongsTo
     */
    public function priority()
    {
        return $this->belongsTo('Antares\Logger\Model\LogPriorities', 'priority_id', 'id');
    }

    /**
     * relation to Brands model
     * 
     * @return BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo('Antares\Brands\Model\Brands', 'brand_id', 'id');
    }

    /**
     * relation to job results
     * 
     * @return BelongsTo
     */
    public function jobs()
    {
        return $this->belongsTo(JobResults::class, 'owner_id', 'id');
    }

    /**
     * Relation to log translations
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translation()
    {
        return $this->hasMany(LogsTranslations::class, 'log_id');
    }

    /**
     * Gets translated operation name
     * 
     * @param String $locale
     * @return String
     */
    public function translated($locale = null)
    {
        return with(new LogDecorator($this))->setLocale($locale)->decorate();
    }

    /**
     * Relation to logs table
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stack()
    {
        return $this->hasOne(NotificationsStack::class, 'log_id', 'id');
    }

}
