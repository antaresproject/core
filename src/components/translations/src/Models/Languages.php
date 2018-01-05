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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */



namespace Antares\Translations\Models;

use Antares\Logger\Traits\LogRecorder;
use Antares\Model\Eloquent;

/**
 * Class Languages
 * @package Antares\Translations\Models
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $icon_code
 */
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'icon_code',
    ];

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

    /**
     * @return string
     */
    public function getIconCodeAttribute() {
        switch($this->code) {
            case 'en':
                return 'us';

            default:
                return $this->code;
        }
    }

}
