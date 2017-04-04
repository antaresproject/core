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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Foundation\Http\Middleware;

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
    }

    /**
     * After sending through pipeline.
     *
     * @return void
     */
    protected function afterSendingThroughPipeline()
    {
        $this->dispatcher->fire('antares.done: admin');
    }

}
