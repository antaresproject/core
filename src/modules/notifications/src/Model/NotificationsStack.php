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


namespace Antares\Notifications\Model;

use Illuminate\Database\Eloquent\Model;
use Antares\Model\User;

class NotificationsStack extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_notifications_stack';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['notification_id', 'author_id', 'variables', 'created_at', 'updated_at'];

    /**
     * Cast values.
     *
     * @var array
     */
    protected $casts = ['variables' => 'json'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Relation to notifications table
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function notification()
    {
        return $this->hasOne(Notifications::class, 'id', 'notification_id');
    }

    /**
     * Relation to notifications table
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function content()
    {
        return $this->hasMany(NotificationContents::class, 'notification_id', 'notification_id');
    }

    /**
     * Relation to stack params table
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function params()
    {
        return $this->hasMany(NotificationsStackParams::class, 'stack_id', 'id');
    }

    /**
     * Relation to stack read table
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function read()
    {
        return $this->hasMany(NotificationsStackRead::class, 'stack_id', 'id');
    }

    /**
     * Relation to stack read table
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function author()
    {
        return $this->hasOne(User::class, 'id', 'author_id');
    }

}
