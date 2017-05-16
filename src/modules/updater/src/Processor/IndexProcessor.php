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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Processor;

use Antares\Updater\Contracts\IndexPresenter as Presenter;
use Antares\Foundation\Processor\Processor;
use Antares\Support\Facades\Foundation;
use Illuminate\Support\Facades\Log;
use Antares\Support\Facades\Memory;
use Illuminate\Http\JsonResponse;
use Exception;

class IndexProcessor extends Processor
{

    /**
     * constructing
     * 
     * @param Presenter $presenter
     */
    public function __construct(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * realize index action version controller
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->presenter->table();
    }

    /**
     * hide alert with system version when new available
     * 
     * @return JsonResponse
     */
    public function hide()
    {
        $response = new JsonResponse();
        try {
            $primary = Memory::make('primary');
            $primary->push('updater.alert.hide', 1);
            $primary->finish();
        } catch (Exception $e) {
            Log::emergency($e);
            $response->setContent(['message' => $e->getMessage()]);
            $response->setStatusCode(302);
        }
        return $response;
    }

    /**
     * update system page
     * 
     * @return \Illuminate\View\View
     */
    public function update()
    {
        $adapter = Foundation::make('antares.version')->getAdapter();
        //$sandbox = Memory::make('primary')->get('sandbox.mode');
        $sandbox = app('request')->get('sandbox');
        return $this->presenter->update($adapter, $sandbox);
    }

}
