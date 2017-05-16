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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Automation\Model;

use Antares\Model\Eloquent;
use Antares\Logger\Traits\LogRecorder;

class JobErrors extends Eloquent
{

    use LogRecorder;

// Disables the log record in this model.
    protected $auditEnabled   = true;
// Disables the log record after 500 records.
    protected $historyLimit   = 500;
// Fields you do NOT want to register.
    protected $dontKeepLogOf  = ['created_at'];
// Tell what actions you want to audit.
    protected $auditableTypes = ['created', 'saved', 'deleted'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_job_errors';

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass = 'JobErrors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['result_job_id', 'code', 'name', 'return'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * relation to job results table
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jobResult()
    {
        return $this->belongsTo(JobResults::class, 'result_job_id', 'id');
    }

}
