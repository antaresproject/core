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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ModuleSettings extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_module_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'component_id', 'module_name', 'name', 'value',
    ];

    public function scopeSearch(Builder $query, $name)
    {
        return $query->where('name', '=', $name);
    }

    public function component()
    {
        return $this->hasOne('Antares\Model\Component', 'id');
    }

    /**
     * executing saving generic module settings
     * 
     * @param String $name
     * @param String $item
     * @param array $options
     */
    public function onSave($name, array $options = array())
    {
        $model   = $this->query()->where('module_name', '=', $name)->get()->first();
        $inserts = [];
        foreach ($options as $key => $value) {
            if ($key == '_token' OR $key == 'validator') {
                continue;
            }
            $inserts[] = [
                'module_name' => $name,
                'name'        => $key,
                'value'       => $value
            ];
        }
        DB::transaction(function() use($model, $inserts, $name) {
            if (!is_null($model)) {
                DB::table($this->table)->where(['module_name' => $name])->delete();
            }
            DB::table($this->table)->insert($inserts);
        });
        return true;
    }

    /**
     * get generic module configuration
     * 
     * @param String $name
     * @param String $item
     * @return array
     */
    public function get($name)
    {
        $collection = $this->query()->where('module_name', '=', $name)->get();
        if (is_null($collection)) {
            return [];
        }
        $return = [];
        $collection->each(function($current) use(&$return) {
            $return[$current->name] = $current->value;
        });
        return $return;
    }

}
