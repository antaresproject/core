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



namespace Antares\Logger\Processor;

use Antares\Logger\Contracts\HistoryPresenter as Presenter;
use Antares\Logger\Contracts\HistoryListener;
use Antares\Foundation\Processor\Processor;
use Antares\Logger\Model\Report;

class HistoryProcessor extends Processor
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
     * default index action
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->presenter->table();
    }

    /**
     * show report details
     * 
     * @param mixed $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return $this->presenter->show($eloquent);
    }

    /**
     * delete report
     * 
     * @param mixed $id
     * @param HistoryListener $listener
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, HistoryListener $listener)
    {
        $model = Report::where('id', $id)->first();
        if (is_null($model)) {
            return $listener->deleteFailed();
        }
        if ($model->delete()) {
            return $listener->deleteSuccess();
        }
        return $listener->deleteFailed();
    }

}
