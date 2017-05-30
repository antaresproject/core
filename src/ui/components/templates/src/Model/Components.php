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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Model;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Antares\Model\Eloquent;
use Exception;

class Components extends Eloquent
{

    /**
     * tablename
     * 
     * @var String
     */
    protected $table = 'tbl_widgets';

    /**
     * has timestamps
     * 
     * @var String
     */
    public $timestamps = false;

    /**
     * can be updated|inserted 
     * 
     * @var array 
     */
    protected $fillable = array('type_id', 'name');

    /**
     * Relation to ui component types
     * 
     * @return BelongsTo
     */
    public function widgetTypes()
    {
        return $this->belongsTo(ComponentTypes::class, 'type_id', 'id');
    }

    /**
     * relation to ui component types
     * 
     * @return BelongsTo
     */
    public function widgetParams()
    {
        return $this->hasMany(ComponentParams::class, 'wid', 'id');
    }

    /**
     * try to save widget
     * 
     * @param array $options
     * @return boolean
     */
    public function save(array $options = array())
    {

        try {
            DB::transaction(function() use($options) {
                $this->fill($options);
                parent::save();
            });

            return true;
        } catch (Exception $e) {
            Log::emergency($e);
            return false;
        }
    }

}
