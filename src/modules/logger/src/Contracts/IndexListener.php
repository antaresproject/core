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



namespace Antares\Logger\Contracts;

use Antares\Datatables\Html\Builder;
use Illuminate\Http\Request;

interface IndexListener
{

    /**
     * system information action
     * 
     * @param Request $request
     * @param Builder $htmlBuilder
     */
    public function system(Request $request, Builder $htmlBuilder);

    /**
     * error log details action
     * 
     * @param String $date
     * @param String $level
     * @param Request $request
     * @param Builder $htmlBuilder
     */
    public function details($date, $level = null);

    /**
     * delete error log action
     * 
     * @param String $date
     */
    public function delete($date);

    /**
     * when download error log
     * 
     * @param String $date
     * @return mixed
     */
    public function download($date);
}
