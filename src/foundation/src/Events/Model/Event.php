<?php

namespace Antares\Foundation\Events\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property strong $namespace
 * @property int    $fire_count
 * @property string $details
 * @property string $created_at
 * @property string $updated_at
 */
class Event extends Model
{

    /** @var string */
    protected $table = 'tbl_events';

    /** @var bool */
    public $timestamps = true;

    /** @var array */
    protected $fillable = ['namespace', 'fire_count', 'details'];

}
