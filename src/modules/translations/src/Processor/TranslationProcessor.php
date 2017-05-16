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

use Antares\Translations\Contracts\TranslationPresenter as Presenter;
use Antares\Translations\Repository\TranslationRepository;
use Antares\Translations\Contracts\TranslationListener;
use Antares\Translations\Models\Translation;
use Antares\Foundation\Processor\Processor;
use Antares\Translations\Models\Languages;
use Illuminate\Support\Facades\Session;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Exception;

class TranslationProcessor extends Processor
{

    /**
     * filesystem instance
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * TranslationRepository instance
     *
     * @var TranslationRepository
     */
    protected $translationRepository;

    /**
     * constructing
     * 
     * @param Presenter $presenter
     */
    public function __construct(Presenter $presenter, TranslationRepository $translationRepository)
    {
        $this->presenter             = $presenter;
        $this->translationRepository = $translationRepository;
    }

    /**
     * default translation action
     * 
     * @param mixed $id
     * @param String $code
     * @return \Illuminate\View\View
     */
    public function index($id, $code = null)
    {
        $locale    = is_null($code) ? app()->getLocale() : $code;
        $locale    = $locale == null ? 'en' : $locale;
        $current   = Languages::where('code', $locale)->first();
        $languages = Languages::query()->get();
        if (!$this->translationRepository->count($id, $locale)) {
            return redirect(handles('antares::translations/sync/' . $id . '/' . $locale));
        }
        return $this->presenter->table($id, $current, $languages, $this->translationRepository->getCategories($id, $locale));
    }

    /**
     * single term translation 
     * 
     * @param mixed $type
     * @param String $code
     * @return JsonResponse
     */
    public function translation($type)
    {
        $keys       = Input::get('keys');
        $code       = Input::get('code');
        $collection = Translation::where('area', $type)->whereIn('key', $keys)->where('locale', $code)->get();

        $map = array_map(function($item) {
            return array_only($item, ['id', 'key', 'value']);
        }, $collection->toArray());
        return new JsonResponse($map, 200);
    }

    /**
     * updates term translation 
     * 
     * @param TranslationListener $listener
     * @param mixed $type
     * @return JsonResponse
     */
    public function update(TranslationListener $listener, $type)
    {
        $params      = Input::all();
        $first       = head(array_keys(array_get($params, 'name')));
        $translation = Translation::whereId($first)->with('language')->first();
        $code        = $translation->language->code;
        if ($this->translationRepository->updateTranslations($params, $type)) {

            return $listener->updateSuccessfull($type, $code);
        }
        return $listener->updateFailed($type, $code);
    }

    /**
     * process changing translation group
     * 
     * @param TranslationListener $listener
     * @param numeric $typeId
     * @param String $group
     * @param String $code
     * @return type
     */
    public function group(TranslationListener $listener, $typeId, $group, $code = null)
    {
        try {
            Session::put('group', $group);
            return redirect(handles("antares::translations/index/{$typeId}/{$code}"));
        } catch (Exception $e) {
            Log::warning($e);
            return $listener->groupFailedFailed($e->getMessage());
        }
    }

    public function updateKey()
    {
        $translation      = Translation::query()->findOrFail(input('id'));
        $translation->key = input('value');
        if ($translation->save()) {
            return new JsonResponse(['message' => trans('antares/translations::messages.response.translation_key_updated')], 200);
        }
        return new JsonResponse(['message' => trans('antares/translations::messages.response.translation_key_update_failed')], 500);
    }

    public function updateTranslation()
    {
        $translation        = Translation::query()->findOrFail(input('id'));
        $translation->value = input('value');
        if ($translation->save()) {
            return new JsonResponse(['message' => trans('antares/translations::messages.response.translation_updated')], 200);
        }
        return new JsonResponse(['message' => trans('antares/translations::messages.response.translation_update_failed')], 500);
    }

    public function deleteTranslation()
    {
        $translation = Translation::query()->findOrFail(input('id'));
        if ($translation->delete()) {
            return new JsonResponse(['message' => trans('antares/translations::messages.response.translation_deleted')], 200);
        }
        return new JsonResponse(['message' => trans('antares/translations::messages.response.translation_delete_failed')], 500);
    }

    public function addTranslation($type, $locale)
    {
        $lang        = Languages::query()->where('code', $locale)->firstOrFail();
        $group       = input('group', 'foundation');
        $translation = new Translation([
            'locale'  => $locale,
            'area'    => $type,
            'lang_id' => $lang->id,
            'group'   => 'antares/' . ($group == 'all' ? 'foundation' : $group),
            'key'     => input('key'),
            'value'   => input('translation'),
        ]);
        if ($translation->save()) {
            return new JsonResponse(['message' => trans('antares/translations::messages.response.translation_saved')], 200);
        }
        return new JsonResponse(['message' => trans('antares/translations::messages.response.translation_save_failed')], 500);
    }

}
