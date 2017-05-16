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

use Illuminate\Http\Request;

interface HistoryListener
{

    /**
     * index default action
     */
    public function index(Request $request);

    /**
     * report details
     */
    public function show($id);

    /**
     * delete report
     */
    public function delete($id);

    /**
     * when delete report failed
     */
    public function deleteFailed();

    /**
     * when delete report completed successfully
     */
    public function deleteSuccess();
}
