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


namespace Antares\Foundation\Http\Controllers\Datatables;

use Antares\Foundation\Http\Controllers\BaseController;
use Antares\Foundation\Processor\FilterProcessor;

class FilterController extends BaseController
{

    /**
     * Processor instance
     *
     * @var FilterProcessor 
     */
    protected $processor;

    /**
     * Constructing
     */
    public function __construct(FilterProcessor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * setups middlewares
     */
    public function setupMiddleware()
    {
        $this->middleware('auth');
    }

    /**
     * stores filter parameter in session
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        return $this->processor->store();
    }

    /**
     * deletes filter parameters from session
     * 
     * @return boolean
     */
    public function destroy()
    {
        return $this->processor->destroy();
    }

    /**
     * updates filter parameter in session
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update()
    {
        return $this->processor->update();
    }

    /**
     * Additional save custom filter params
     * 
     * @return \Illuminate\Http\Response
     */
    public function save()
    {
        return $this->processor->save();
    }

    /**
     * Additional delete custom filter params
     * 
     * @return \Illuminate\Http\Response
     */
    public function delete()
    {
        return $this->processor->delete();
    }

}
