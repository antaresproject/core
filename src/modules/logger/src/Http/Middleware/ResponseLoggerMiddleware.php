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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Logger\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Antares\Logger\Jobs\LogTask;
use Illuminate\Http\Request;
use Closure;

class ResponseLoggerMiddleware
{

    use DispatchesJobs;

    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response)
    {
        if (!$this->excluded($request)) {
            $task = new LogTask($request, $response);

            if ($queueName = config('request-logger.queue')) {
                $this->dispatch(is_string($queueName) ? $task->onQueue($queueName) : $task);
            } else {
                $task->handle();
            }
        }
    }

    protected function excluded(Request $request)
    {
        $exclude = config('request-logger.exclude');
        if (empty($exclude)) {
            return false;
        }
        foreach ($exclude as $path) {
            if ($request->is($path))
                return true;
        }

        return false;
    }

}
