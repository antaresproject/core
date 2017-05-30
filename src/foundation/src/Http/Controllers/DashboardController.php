<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Foundation\Http\Controllers;

use Antares\Contracts\Foundation\Listener\Account\ProfileDashboard as Listener;
use Antares\Users\Processor\Account\ProfileDashboard as Processor;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Application;
use Illuminate\View\View;

class DashboardController extends AdminController implements Listener
{

    /**
     * application instance
     *
     * @var Application
     */
    protected $app;

    /**
     * The layout that should be used for responses.
     * 
     * @var String
     */
    protected $layout = 'antares/foundation::layouts.admin.dashboard';

    /**
     * Dashboard controller routing.
     *
     * @param Processor  $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
        $this->app       = app();
        parent::__construct();
    }

    /**
     * Setup controller middleware.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.can:show-dashboard', ['only' => ['show'],]);
    }

    /**
     * Show User Dashboard.
     *
     * GET (:antares)/
     * @return mixed
     */
    public function show()
    {
        view()->share('content_class', 'page-dashboard');
        return $this->processor->show($this);
    }

    /**
     * Show missing pages.
     *
     * GET (:antares) return 404
     * @return mixed
     */
    public function missing()
    {
        throw new NotFoundHttpException('Controller method not found.');
    }

    /**
     * Response to show dashboard.
     *
     * @return mixed
     */
    public function showDashboard()
    {
        set_meta('title', trans('antares/foundation::title.home'));
        $this->app->make('events')->fire('version.check');
        if (auth()->user()->hasRoles('member')) {
            return view('antares/foundation::dashboard.member');
        }

        return view('antares/foundation::dashboard.index');
    }

    /**
     * zero data page
     * 
     * @return View
     */
    public function notAllowed()
    {
        return view('antares/foundation::dashboard.not-allowed');
    }

    /**
     * sample error
     * 
     * @throws \Exception
     */
    public function error()
    {
        throw new \Exception('Sample error');
    }

}
