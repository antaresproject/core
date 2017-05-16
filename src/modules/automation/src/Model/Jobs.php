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

use Antares\Model\Component;
use Antares\Model\Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Antares\Logger\Traits\LogRecorder;

class Jobs extends Eloquent
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
    protected $table = 'tbl_jobs';

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass = 'Jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['category_id', 'component_id', 'active', 'name', 'value'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Cast values.
     *
     * @var array
     */
    protected $casts = ['value' => 'array'];

    /**
     * Query scope for active jobs
     *
     * @param  object     $query
     *
     * @return void
     */
    public function scopeActive($query)
    {
        $query->where('active', 1);
    }

    /**
     * relation to job results
     * 
     * @return HasMany
     */
    public function jobResults()
    {
        return $this->hasMany(JobResults::class, 'job_id', 'id');
    }

    /**
     * relation to components
     * 
     * @return HasOne
     */
    public function component()
    {
        return $this->hasOne(Component::class, 'id', 'component_id');
    }

    /**
     * Relations to category model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(JobsCategory::class, 'category_id');
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::automation/edit/{id}');
    }

}
