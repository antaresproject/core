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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications\Http\Controllers\Admin;

use Antares\Notifications\Processor\LogsProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Notifications\Contracts\LogsListener;

class LogsController extends AdminController implements LogsListener
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
        $this->middleware("antares.can:antares/notifications::notifications-list", ['only' => ['index']]);
    }

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        return $this->processor->index();
    }

    /**
     * {@inheritdoc}
     */
    public function preview($id)
    {
        return $this->processor->preview($id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id = null)
    {
        return $this->processor->delete($this, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSuccess()
    {
        app('antares.messages')->add('success', trans('antares/notifications::logs.notification_delete_success'));
        return redirect()->to(handles('antares::notifications/logs/index'));
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFailed()
    {
        app('antares.messages')->add('error', trans('antares/notifications::logs.notification_delete_failed'));
        return redirect()->to(handles('antares::notifications/logs/index'));
    }

}
