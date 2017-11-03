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
 * @version    0.9.2
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
        app(\Antares\UI\UIComponents\Adapter\GridStackAdapter::class)->scripts();
        view()->share('content_class', 'app-content--gridstack page-dashboard page-dashboard-html widgets-html-page');
        return $this->processor->show($this);
    }

    /**
     * Boot a fresh copy of the application and get the routes.
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    protected function getFreshApplicationRoutes()
    {
        return tap($this->getFreshApplication()['router']->getRoutes(), function ($routes) {
            $routes->refreshNameLookups();
            $routes->refreshActionLookups();
        });
    }

    /**
     * Get a fresh application instance.
     *
     * @return \Illuminate\Foundation\Application
     */
    protected function getFreshApplication()
    {
        return tap(require app(\Antares\Foundation\Application::class)->bootstrapPath() . '/app.php', function ($app) {
            $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        });
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
        if (!auth()->guest() && auth()->user()->hasRoles('member')) {
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
