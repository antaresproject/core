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
use Antares\Translations\Models\Languages;
use Antares\Model\Eloquent;

class LogsTranslations extends Eloquent
{

    /**
     * Tablename
     * 
     * @var string
     */
    public $table = 'tbl_logs_translations';

    /**
     * Fillable table column
     *
     * @var array
     */
    protected $fillable = array('lang_id', 'log_id', 'raw', 'text');

    /**
     * Whether table has timestamps columns
     *
     * @var type 
     */
    public $timestamps = false;

    /**
     * Relation to Languages model
     * 
     * @return BelongsTo
     */
    public function lang()
    {
        return $this->belongsTo(Languages::class, 'lang_id');
    }

    /**
     * Relation to Logs model
     * 
     * @return BelongsTo
     */
    public function log()
    {
        return $this->belongsTo(Logs::class, 'log_id');
    }

}
