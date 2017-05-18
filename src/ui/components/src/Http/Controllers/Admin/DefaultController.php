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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Http\Controllers\Admin;

use Antares\UI\UIComponents\Processor\DefaultProcessor as Processor;
use Antares\UI\UIComponents\Contracts\Viewer as ViewContract;
use Antares\Foundation\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Input;

class DefaultController extends AdminController implements ViewContract
{

    /**
     * Implements instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * Route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
    }

    /**
     * Display the specified resource.
     * GET /ui-components/{id}
     *
     * @param  mixed  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->processor->show($id);
    }

    /**
     * Saving grid parameters
     * 
     * GET /ui-components/{id}
     *
     * @return Response
     */
    public function grid()
    {
        return $this->processor->positions(Input::all());
    }

    /**
     * Shows full content of ui component
     * GET /ui-components/{id}
     *
     * @return Response
     */
    public function view($id)
    {
        $component = $this->processor->view($id);

        return response($component['component']->__toString(), 200);
    }

}
