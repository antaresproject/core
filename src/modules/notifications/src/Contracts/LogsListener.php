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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications\Contracts;

use Illuminate\Support\MessageBag;

interface LogsListener
{

    /**
     * Index default action
     * 
     * @return \Illuminate\View\View
     */
    public function index();

    /**
     * Preview notification log
     * 
     * @param mixed $id
     * @return \Illuminate\View\View
     */
    public function preview($id);

    /**
     * Deletes notification log
     * 
     * @param mixed $id
     * @return RedirectResponse
     */
    public function delete($id = null);

    /**
     * When deletion of notification log completed successfully
     * 
     * @return RedirectResponse
     */
    public function deleteSuccess();

    /**
     * When deletion of notification log failed
     * 
     * @return RedirectResponse
     */
    public function deleteFailed();
}
