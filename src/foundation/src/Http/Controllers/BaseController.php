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

use Antares\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Antares\Routing\Traits\ControllerResponseTrait;

abstract class BaseController extends Controller
{

    use ControllerResponseTrait;

    /**
     * Processor instance.
     *
     * @var object
     */
    protected $processor;

    /**
     * Base controller construct method.
     */
    public function __construct()
    {
        $this->setupFilters();
        $this->setupMiddleware();
    }

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupFilters()
    {

        //
    }

    /**
     * Setup controller middleware.
     *
     * @return void
     */
    abstract protected function setupMiddleware();

    /**
     * Show missing pages.
     *
     * GET (:antares) return 404
     *
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function missingMethod($parameters = [])
    {
        return Response::view('antares/foundation::dashboard.missing', $parameters, 404);
    }
    
    /**
     * 
     * @return \Antares\Foundation\Processor\Processor
     */
    public function getProcessor() {
        return $this->processor;
    }

}
