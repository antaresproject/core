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


namespace Antares\Notifications\Http\Controllers\Admin;

use Antares\Notifications\Processor\SidebarProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;

class SidebarController extends AdminController
{

    /**
     * implments instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware("antares.auth");
    }

    /**
     * Deletes sidebar item
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        return $this->processor->delete();
    }

    /**
     * Marks notification as read
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function read()
    {
        return $this->processor->read();
    }

    /**
     * Gets sidebar notifications by ajax
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {
        return $this->processor->get();
    }

    /**
     * Clears notifications
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear($type = null)
    {
        return $this->processor->clear($type);
    }

}
