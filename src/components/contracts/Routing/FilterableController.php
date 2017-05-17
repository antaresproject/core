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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
 namespace Antares\Contracts\Routing;

interface FilterableController
{
    /**
     * Register a "before" filter on the controller.
     *
     * @param  \Closure|string  $filter
     * @param  array  $options
     *
     * @return void
     */
    public function beforeFilter($filter, array $options = []);

    /**
     * Register an "after" filter on the controller.
     *
     * @param  \Closure|string  $filter
     * @param  array  $options
     *
     * @return void
     */
    public function afterFilter($filter, array $options = []);

    /**
     * Remove the given before filter.
     *
     * @param  string  $filter
     *
     * @return void
     */
    public function forgetBeforeFilter($filter);

    /**
     * Remove the given after filter.
     *
     * @param  string  $filter
     *
     * @return void
     */
    public function forgetAfterFilter($filter);

    /**
     * Get the registered "before" filters.
     *
     * @return array
     */
    public function getBeforeFilters();

    /**
     * Get the registered "after" filters.
     *
     * @return array
     */
    public function getAfterFilters();
}
