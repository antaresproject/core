<?php

namespace Antares\Events\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Event
 * @property string $namespace
 * @property string $name
 * @property string $description
 * @property integer $fire_count
 * @package Antares\Events\Model
 */
class Event extends Model
{
    /**
     * tablename
     *
     * @var String
     */
    protected $table = 'tbl_events';

    /**
     * has timestamps
     *
     * @var String
     */
    public $timestamps = true;

    /**
     * can be updated|inserted
     *
     * @var array
     */
    protected $fillable = array('namespace', 'fire_count');
}