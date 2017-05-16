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

use Antares\Logger\Traits\LogRecorder;
use Antares\Model\Eloquent;

class Languages extends Eloquent
{

    use LogRecorder;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_languages';

    /**
     * The attributes that should be filled.
     *
     * @var array
     */
    protected $fillable = ['code', 'name'];

    /**
     * tables doesnt have timestamp columns
     *
     * @var boolean 
     */
    public $timestamps = false;

    /**
     * relation to Translation model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany(Translation::class, 'lang_id', 'id');
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl($id = null)
    {
        return !is_null($id) ? handles('antares::translations/languages/index') : handles('antares::translations/languages/index');
    }

}
