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



namespace Antares\Support\Providers\Traits;

use Illuminate\Routing\Router;

trait FilterProviderTrait {
	/**
	 * Register route filters.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 *
	 * @return void
	 */
	protected function registerRouteFilters(Router $router) {
		foreach ( ( array ) $this->before as $before ) {
			$router->before ( $before );
		}
		
		foreach ( ( array ) $this->after as $after ) {
			$router->after ( $after );
		}
		
		foreach ( ( array ) $this->filters as $name => $filter ) {
			$router->filter ( $name, $filter );
		}
	}
}
