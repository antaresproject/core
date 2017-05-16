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



namespace Antares\Translations\Repository;

use Antares\Foundation\Repository\AbstractRepository;
use Antares\Translations\Models\Translation;
use Antares\Translations\Models\Languages;
use Illuminate\Support\Facades\DB;

class LanguageRepository extends AbstractRepository
{

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Antares\Translations\Models\Languages';
    }

    /**
     * fetch all languages
     * 
     * @return \Illuminate\Support\Collection
     */
    public function fetchAll()
    {
        return $this->all();
    }

    /**
     * store new language in database
     * 
     * @param array $data
     */
    public function insert($data)
    {
        DB::transaction(function() use($data) {
            $data['code'] = strtolower($data['code']);
            $model        = new Languages($data);
            $model->save();
            $inserts      = $this->prepareMultiInsert($data['code']);
            DB::table('tbl_translations')->insert($inserts);
        });
    }

    /**
     * prepare data before multiinsert translations from default
     * 
     * @param String $code
     * @return array
     */
    protected function prepareMultiInsert($code)
    {
        $default      = Languages::where('is_default', 1)->first()->code;
        $translations = Translation::where('locale', $default)->get()->toArray();
        $translation  = new Translation();
        $guarded      = $translation->getGuarded();
        $langId       = Languages::where('code', $code)->first()->id;
        $inserts      = array_map(function($current) use($code, $langId, $guarded) {
            $current['locale']  = $code;
            $current['lang_id'] = $langId;
            return array_except($current, $guarded);
        }, $translations);

        return $inserts;
    }

    /**
     * Finds languages by specified locale
     * 
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByLocale(array $params = [])
    {
        return (empty($params)) ? $this->fetchAll() : $this->makeModel()->whereIn('code', array_values($params))->get();
    }

}
