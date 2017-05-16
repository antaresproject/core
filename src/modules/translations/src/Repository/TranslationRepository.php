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
use Antares\Translations\Models\Languages;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class TranslationRepository extends AbstractRepository
{

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Antares\Translations\Models\Translation';
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
     * get translation groups
     * 
     * @param String $area
     * @param String $locale
     * @return \Illuminate\Database\Query\Builder
     */
    public function getGroups($area, $locale)
    {
        return $this->model->where('area', $area)->whereHas('language', function($query) use($locale) {
                            $query->where('code', $locale);
                        })->groupBy('group')->orderBy('group', 'desc')
                        ->get();
    }

    /**
     * get translations by type and locale
     * 
     * @param String $area
     * @param String $locale
     * @return \Illuminate\Database\Query\Builder
     */
    public function getList($area, $locale)
    {
        $model = $this->app->make($this->model());
        $group = str_replace(["\n", "\r", ' '], '', $this->app->make('languages')->getTranslationNamedGroup());

        return $model->where('area', $area)->where(function($query) use($locale, $group) {
                    $query->where('locale', $locale);
                    if (strlen($group) > 0) {
                        $query->where('group', $group);
                    }
                });
    }

    /**
     * saves translations in database 
     * 
     * @param type $path
     * @param type $type
     * @return boolean
     */
    public function importTranslations($path, $type)
    {
        $separator = app('config')->get('antares/translations::export.separator');
        $content   = explode(PHP_EOL, file_get_contents($path));
        $list      = [];
        foreach ($content as $index => $line) {
            if ($index == 0) {
                continue;
            }
            array_push($list, explode($separator, $line));
        }


        $locale   = $list[0][1];
        $language = Languages::where('code', $locale)->first();
        try {
            DB::transaction(function() use($language, $list, $type) {
                $area   = request()->segment(6);
                DB::table('tbl_translations')->where('lang_id', $language->id)->where('area', $area)->delete();
                $insert = [];
                foreach ($list as $item) {
                    if (!isset($item[2]) or ! isset($item[3])) {
                        continue;
                    }
                    try {
                        $value = iconv('WINDOWS-1250', 'UTF-8', $item[3]);
                    } catch (Exception $ex) {
                        $value = $item[3];
                    }

                    $insert[] = [
                        'locale'  => $language->code,
                        'area'    => $area,
                        'group'   => 'antares/' . $item[0],
                        'lang_id' => $language->id,
                        'key'     => trim($item[2], '"'),
                        'value'   => trim($value, '"'),
                    ];
                }
                DB::table('tbl_translations')->insert($insert);
            });
            return true;
        } catch (Exception $e) {
            Log::warning($e);
            return false;
        }
    }

    /**
     * delete translations using collection
     * 
     * @param mixed $langId
     * @param String $area
     * @param \Illuminate\Support\Collection $list
     * @return null|boolean
     */
    public function deleteByCollection($langId, $area, $list)
    {
        if (empty($list)) {
            return;
        }

        foreach ($list as $group => $element) {
            DB::table('tbl_translations')
                    ->where('lang_id', $langId)
                    ->where('group', $group)
                    ->where('area', $area)
                    ->where('key', key($element))
                    ->where('value', current($element))->delete();
        }
        return true;
    }

    /**
     * mass update translations
     * 
     * @param array $params
     * @param mixed $type
     * @return boolean
     */
    public function updateTranslations($params, $type)
    {
        if (is_null($translations = array_get($params, 'name'))) {
            return false;
        }
        try {
            DB::transaction(function() use($translations, $type) {
                foreach ($translations as $id => $value) {
                    $where        = [
                        'id'   => $id,
                        'area' => $type
                    ];
                    $model        = $this->makeModel()->newQuery()->where($where)->first();
                    $model->value = $value;
                    $model->save();
                }
            });
            return true;
        } catch (Exception $ex) {
            Log::emergency($ex);
            return false;
        }
    }

    /**
     * Gets translation categories
     * 
     * @param mixes $id
     * @param String $locale
     * @return array
     */
    public function getCategories($id, $locale)
    {
        $groups    = $this->getGroups($id, $locale)->toArray();
        $available = antares('memory')->get('extensions.available');
        $list      = ['all' => 'All', 'foundation' => trans('Core')];

        array_walk($groups, function($item) use($available, &$list) {
            $group = str_replace('antares/', '', $item['group']);
            foreach ($available as $extension) {
                if ($group == $extension['name']) {
                    $list[$group] = $extension['full_name'];
                }
            }
        });
        $return = [
            'all'           => 'All',
            'core'          => [
                'label'      => trans('antares/translations::messages.group.core'),
                'extensions' => []
            ],
            'configuration' => [
                'label'      => trans('antares/translations::messages.group.configuration'),
                'extensions' => []
            ],
        ];
        foreach ($list as $name => $value) {
            if (in_array($name, ['foundation', 'widgets', 'search', 'control'])) {
                $return['core']['extensions'][$name] = $value;
            } elseif (in_array($name, ['two_factor_auth', 'api'])) {
                $return['configuration']['extensions'][$name] = $value;
            } else {
                $return[$name] = $value;
            }
        }
        return $return;
    }

    /**
     * Counts translations by area
     * 
     * @param String $area
     * @return mixed
     */
    public function count($area, $locale)
    {
        return $this->makeModel()->newQuery()->where(['area' => $area, 'locale' => $locale])->count();
    }

}
