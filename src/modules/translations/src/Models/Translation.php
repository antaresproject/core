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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Translations\Models;

use Antares\Model\Eloquent;

/**
 * Translation model
 *
 * @property integer $id
 * @property integer $status
 * @property string  $locale
 * @property string  $group
 * @property string  $key
 * @property string  $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Translation extends Eloquent
{

    const STATUS_SAVED   = 0;
    const STATUS_CHANGED = 1;

    /**
     * tablename
     *
     * @var String
     */
    protected $table = 'tbl_translations';

    /**
     * guarded list of columns
     *
     * @var array 
     */
    protected $guarded = array('id', 'created_at', 'updated_at');

    /**
     * belongs to relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo('Antares\Translations\Models\Languages', 'lang_id', 'id');
    }

}
