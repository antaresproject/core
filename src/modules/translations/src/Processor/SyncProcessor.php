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

use Antares\Translations\Contracts\SyncListener;
use Antares\Translations\Processor\Synchronizer;
use Antares\Translations\Models\Translation;
use Antares\Foundation\Processor\Processor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class SyncProcessor extends Processor
{

    /**
     * Synchronizer instance
     *
     * @var Synchronizer 
     */
    protected $synchronizer;

    /**
     * constructing
     * 
     * @param Synchronizer $synchronizer
     */
    public function __construct(Synchronizer $synchronizer)
    {
        $this->synchronizer = $synchronizer;
    }

    /**
     * default index action
     * 
     * @param SyncListener $listener
     * @param String $area
     * @param String $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(SyncListener $listener, $area, $locale = null)
    {
        $lang = is_null($locale) ? app()->getLocale() : $locale;
        try {
            if (Translation::where('area', $area)->where('locale', $locale)->count()) {
                return redirect(handles('antares::translations/index/' . $area . '/' . $lang));
            }
            if (!is_null($locale)) {
                $this->synchronizer->synchronize(lang($locale), $area);
            } else {
                DB::transaction(function() use($area) {
                    $langs = app('languages')->langs();
                    foreach ($langs as $lang) {
                        $this->synchronizer->synchronize($lang, $area);
                    }
                });
            }
            return $listener->syncSuccess($area, $lang);
        } catch (Exception $e) {
            vdump($e);
            exit;
            Log::warning($e);
            return $listener->syncFailed($area, $lang);
        }
    }

}
