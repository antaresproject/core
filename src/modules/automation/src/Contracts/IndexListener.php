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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Automation\Contracts;

use Illuminate\Http\Request;

interface IndexListener
{

    /**
     * index default action
     */
    public function index();

    /**
     * shows job details
     */
    public function show($id);

    /**
     * when show job details failed
     */
    public function showFailed();

    /**
     * job edit form
     */
    public function edit($id);

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
     * runs single job
     */
    public function run($id);

    /**
     * response when run job failed
     */
    public function runFailed();

    /**
     * response when run job success
     */
    public function runSuccess();
}
