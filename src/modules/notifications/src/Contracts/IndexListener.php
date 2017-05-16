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

interface IndexListener
{

    /**
     * job edit form
     */
    public function edit($id, $locale = 'en');

    /**
     * update single job
     */
    public function update();

    /**
     * when update job failed
     */
    public function updateFailed();

    /**
     * when update job completed successfully
     */
    public function updateSuccess();

    /**
     * sends test notification
     */
    public function sendtest();

    /**
     * when sending preview notification notification failed
     */
    public function sendFailed();

    /**
     * when sending preview notification completed successfully
     */
    public function sendSuccess();

    /**
     * preview notification
     */
    public function preview();

    /**
     * changes notification status
     */
    public function changeStatus($id);

    /**
     * when changing notification status completed successfully
     */
    public function changeStatusSuccess();

    /**
     * when changing notification status failed
     */
    public function changeStatusFailed();

    /**
     * create new notification
     */
    public function create($type = null);

    /**
     * when storing new notification failed on validation
     */
    public function storeValidationFailed($errors);

    /**
     * when storing new notification
     */
    public function store();

    /**
     * when creation notification completed successfully
     */
    public function createSuccess();

    /**
     * when creation notification failed
     */
    public function createFailed();

    /**
     * deletes custom notification
     */
    public function delete($id);

    /**
     * when deletion of custom notification completed successfully
     */
    public function deleteSuccess();

    /**
     * when deletion of custom notification failed
     */
    public function deleteFailed();
}
