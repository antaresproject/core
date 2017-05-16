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

namespace Antares\Updater\Contracts;

interface BackupListener
{

    /**
     * index default action
     */
    public function index();

    /**
     * restoring application from backup
     */
    public function restore($id);

    /**
     * Delete backup job from queue
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id);

    /**
     * When deleteing backup job completed successfully
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSuccess();

    /**
     * When deleteing backup job failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFailed();
}
