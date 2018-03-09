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



namespace Antares\Translations;

use Antares\Translations\Models\Languages as LanguagesEloquent;
use Antares\Translations\Repository\LanguageRepository;
use Illuminate\Support\Facades\Session;

class Languages
{

    /**
     * get list of languages
     * 
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return LanguagesEloquent::select(['id', 'code', 'name'])->whereNotIn('code', [$this->getLocale()])->get();
    }

    /**
     * get selected or default locale
     * 
     * @return String
     */
    public function getLocale()
    {
        $localeFromSession = Session::get('locale');
        return is_null($localeFromSession) ? 'en' : $localeFromSession;
    }

    /**
     * get current locale
     * 
     * @return String
     */
    public function current()
    {
        return LanguagesEloquent::where('code', $this->getLocale())->first();
    }

    /**
     * gets translations group
     * 
     * @return String|null
     */
    public function getTranslationsGroup()
    {
        return Session::get('group');
    }

    /**
     * get translations named group
     * 
     * @return String
     */
    public function getTranslationNamedGroup()
    {
        $group = $this->getTranslationsGroup();
        if (is_null($group)) {
            return;
        }
        return $group == 'all' ? null : 'antares/' . $group;
    }

    /**
     * get list of languages
     * 
     * @return \Illuminate\Support\Collection
     */
    public function langs()
    {
        return app(LanguageRepository::class)->fetchAll();
    }

    /**
     * Finds languages by specified locale
     * 
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    public function getMatched(array $params = [])
    {
        return app(LanguageRepository::class)->findByLocale($params);
    }

}
