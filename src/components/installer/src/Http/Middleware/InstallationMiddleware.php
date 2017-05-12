<?php

namespace Antares\Installation\Http\Middleware;

use Antares\Installation\Progress;
use Illuminate\Routing\Router;
use Illuminate\Http\Request;
use Closure;

class InstallationMiddleware {

    /**
     * Progress instance.
     *
     * @var Progress
     */
	protected $progress;

	/**
	 * Router instance.
	 *
	 * @var Router
	 */
	protected $router;

	/**
	 * Target router name.
	 *
	 * @var string
	 */
	protected static $targetRouterName = 'installation.installer.progress.index';

    /**
     * InstallationMiddleware constructor.
     * @param Progress $progress
     * @param Router $router
     */
	public function __construct(Progress $progress, Router $router) {
		$this->progress = $progress;
		$this->router   = $router;
	}

	/**
	 * @param Request $request
	 * @param Closure $next
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function handle(Request $request, Closure $next) {
		$routeName = $this->router->current()->getName();

		if( ! $this->progress->isFailed() && $this->progress->isRunning() && ! str_contains($routeName, 'installation.installer.progress') ) {
			return redirect()->to(handles('antares::install/progress'));
		}

		return $next($request);
	}

}
