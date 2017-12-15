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

namespace Antares\Foundation\Http\Middleware;

use Antares\Events\SystemReady\AdminDone;
use Antares\Events\SystemReady\AdminReady;
use Antares\Events\SystemReady\AdminStarted;
use Antares\Events\SystemReady\MenuReady;
use Closure;
use Illuminate\Contracts\Events\Dispatcher;

class UseBackendTheme
{

    /**
     * The event dispatcher implementation.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (!auth()->guest() && user()->hasRoles(['client', 'memeber'])) {
            return $next($request);
        }




        app()->setLocale(user_meta('language', 'en'));

        $this->beforeSendingThroughPipeline();

        $response = $next($request);

        $this->afterSendingThroughPipeline();
        return $response;
    }

    /**
     * Before sending through pipeline.
     *
     * @return void
     */
    protected function beforeSendingThroughPipeline()
    {
        $this->dispatcher->fire('antares.started: admin');
        $this->dispatcher->fire('antares.ready: admin');
        $this->dispatcher->fire('antares.ready: menu');

        $this->dispatcher->fire(new AdminStarted());
        $this->dispatcher->fire(new AdminReady());
        $this->dispatcher->fire(new MenuReady());
    }

    /**
     * After sending through pipeline.
     *
     * @return void
     */
    protected function afterSendingThroughPipeline()
    {
        $this->dispatcher->fire('antares.done: admin');
        $this->dispatcher->fire(new AdminDone());
    }

}
