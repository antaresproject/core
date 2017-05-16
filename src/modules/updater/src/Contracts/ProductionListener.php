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

interface ProductionListener
{

    /**
     * backup production application
     */
    public function backup();

    /**
     * validate whether sandbox can be set as production
     */
    public function validate();

    /**
     * finish migration process
     */
    public function finish();

    /**
     * rolling back production update
     */
    public function rollback();

    /**
     * response when every iteration of production update has completed successfully
     * 
     * @param mixed $data
     * @return \Illuminate\View\View
     */
    public function success($data);

    /**
     * response when production has been not updated
     * 
     * @param mixed $data
     * @return \Illuminate\View\View
     */
    public function failed($data);

    /**
     * response when production has been updated successfully
     */
    public function installed();
}
