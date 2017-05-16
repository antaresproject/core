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
use Antares\Logger\Traits\LogRecorder;
use Antares\Model\Eloquent;
use Antares\Model\User;

class LogsLoginDevices extends Eloquent
{

    use LogRecorder;

    /**
     * @var string
     */
    public $table = 'tbl_logs_login_devices';

    /**
     * Cast values.
     *
     * @var array
     */
    protected $casts = ['location' => 'json'];

    /**
     * fillable attributes
     * 
     * @var array 
     */
    protected $fillable = array('user_id', 'log_id', 'name', 'ip_address', 'browser', 'system', 'machine', 'location');

    /**
     * Low priority to log notifications
     *
     * @var String 
     */
    protected $priority = 'low';

    /**
     * relation to Logs model
     * 
     * @return BelongsTo
     */
    public function log()
    {
        return $this->belongsTo(Logs::class, 'log_id', 'id');
    }

    /**
     * relation to User model
     * 
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::logger/devices/{id}/edit');
    }

}
