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

namespace Antares\Model;

use Antares\Logger\Traits\LogRecorder;
use Carbon\Carbon;

/**
 * Class User
 * @property int $id
 * @property string $email
 * @property string $fullname
 * @property bool $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class UserActivity extends Eloquent
{

    use LogRecorder;

    // Disables the log record in this model.
    protected $auditEnabled   = true;
    // Disables the log record after 500 records.
    protected $historyLimit   = 500;
    // Fields you do NOT want to register.
    protected $dontKeepLogOf  = ['created_at', 'updated_at'];
    // Tell what actions you want to audit.
    protected $auditableTypes = ['created', 'saved', 'deleted'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_users_activity';

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass = 'UserActivity';

    /**
     * The attributes that should be filled.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'last_activity'
    ];

    /**
     * relation to User
     *
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
