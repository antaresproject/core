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



namespace Antares\Translations\Processor;

use Antares\Translations\Repository\TranslationRepository;
use Antares\Translations\Models\Translation;
use Antares\Translations\Models\Languages;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;

class Synchronizer
{

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * TranslationRepository instance
     *
     * @var TranslationRepository 
     */
    protected $translationRespository;

    /**
     * hints collection
     *
     * @var array
     */
    protected $hints;

    /**
     * constructing
     * 
     * @param Filesystem $filesystem
     * @param TranslationRepository $translationRespository
     * @param LanguageRepository $languageRepository
     */
    public function __construct(Filesystem $filesystem, TranslationRepository $translationRespository)
    {
        $this->filesystem             = $filesystem;
        $this->translationRespository = $translationRespository;
        $this->hints                  = app('translator')->getLoader()->getHints();
    }

    /**
     * synchronize translations between database and translation files
     * 
     * @param type $lang
     * @param String $area
     */
    public function synchronize($lang, $area)
    {
        $locale       = $lang->code;
        $translations = $this->translationsFromFiles($locale);
        if (empty($translations)) {
            $translations = $this->translationsFromFiles(lang()->code);
        }
        $items   = Translation::where('locale', $locale)->where('area', $area)->get();
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item->group][$item->key] = $item->value;
        }
        $deletes = [];

        foreach ($grouped as $name => $group) {
            if (!isset($translations[$name])) {
                continue;
            }
            $deleted = array_diff($group, $translations[$name]);
            if (!empty($deleted)) {
                $deletes[$name] = $deleted;
            }
        }
        $this->delete($lang->id, $area, $deletes);
        $this->insert($locale, $area, $translations);
    }

    /**
     * gets translations from files
     * 
     * @return array
     */
    public function translationsFromFiles($locale)
    {
        $grouped = [];
        foreach ($this->hints as $name => $hint) {
            $languageDirectory = $hint . DIRECTORY_SEPARATOR . $locale;
            if (!is_dir($languageDirectory)) {
                continue;
            }
            $files = $this->filesystem->allFiles($languageDirectory);
            foreach ($files as $filename) {
                $file         = str_replace('.' . $filename->getExtension(), '', $filename->getFilename());
                $translations = array_dot(require $filename->getRealPath());
                foreach ($translations as $key => $translation) {
                    $grouped[$name] [$file . '.' . $key] = $translation;
                }
            }
        }
        return $grouped;
    }

    /**
     * insert translations
     * 
     * @param String $locale
     * @param String $area
     * @param array $translations
     */
    protected function insert($locale, $area, array $translations)
    {
        $insert = [];
        $langId = Languages:: where('code', $locale)->first()->id;


        foreach ($translations as $module => $translationMap) {
            $currents = DB::table('tbl_translations')->where(['lang_id' => $langId, 'group' => $module, 'area' => $area])->get();
            $use      = [];
            foreach ($currents as $current) {
                $use[$current->lang_id][$current->group][$current->key] = $current->value;
            }
            array_walk($translationMap, function($row, $key) use($locale, $module, $area, $langId, &$insert, $use) {
                if (!isset($use[$langId][$module][$key])) {
                    $insert[] = [
                        'locale'  => $locale,
                        'group'   => $module,
                        'area'    => $area,
                        'lang_id' => $langId,
                        'key'     => $key,
                        'value'   => $row,
                    ];
                }
            });
        }
        if (!empty($insert)) {
            DB::table('tbl_translations')->insert($insert);
        }
    }

    /**
     * get translations from source files
     * 
     * @param array $files
     * @return array
     */
    protected function translations(array $files = array())
    {
        $return = [];
        foreach ($files as $file) {
            $translation = $this->getStringsBetween($file->getContents(), 'trans(\'', "')");
            $translation = array_filter($translation, function($element) {
                if (str_contains($element, ', {')) {
                    return false;
                }
                return $element;
            });
            $translationWithParams = $this->getStringsBetween($file->getContents(), 'trans(\'', "', {");
            $translation           = array_merge($translation, $translationWithParams);
            $return                = array_merge($return, $translation);
        }
        return $return;
    }

    /**
     * search all occurences between strings
     * 
     * @param String $string
     * @param String $start
     * @param String $end
     * @return String
     */
    protected function getStringsBetween($string, $start, $end)
    {

        $pattern = sprintf(
                '/%s(.*?)%s/', preg_quote($start), preg_quote($end)
        );

        preg_match_all($pattern, $string, $matches);

        return $matches[1];
    }

    /**
     * deletes translations
     * 
     * @param mixed $langId
     * @param array $list
     * @return boolean
     */
    protected function delete($langId, $area, array $list)
    {
        return $this->translationRespository->deleteByCollection($langId, $area, $list);
    }

}
