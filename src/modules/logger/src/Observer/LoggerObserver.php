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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Observer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class LoggerObserver
{

    /**
     * Runs after new log has been saved
     * 
     * @param String $model
     */
    public function created(Model $model)
    {
        $langs = app('languages')->langs();
        DB::beginTransaction();
        try {
            foreach ($langs as $lang) {
                $this->saveTranslatedMessage($model, $lang);
            }
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }
        DB::commit();
        return true;
    }

    /**
     * Saves translated message
     * 
     * @param Model $model
     * @param String $message
     * @param array $lang
     * @return boolean
     */
    protected function saveTranslatedMessage(Model $model, $lang)
    {
        if (!$model->translation->isEmpty()) {
            return false;
        }
        $message = $model->translated($lang->code);
        return $model->translation()->getModel()->newInstance([
                    'log_id'  => $model->id,
                    'lang_id' => array_get($lang, 'id'),
                    'raw'     => strip_tags($message),
                    'text'    => $message
                ])->save();
    }

}
