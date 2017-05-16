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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Tester\Http\Controllers\Admin;

use Antares\Tester\Processor\CollectiveProcessor as Processor;
use Antares\Tester\Contracts\TesterProcess as ProcessContract;
use Antares\Tester\Contracts\TesterView as ViewContract;
use Antares\Foundation\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;

class CollectiveController extends AdminController implements ViewContract, ProcessContract
{

    /**
     * implments instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.can:antares/tester::tools-tester', ['only' => ['index', 'run', 'prepare'],]);
    }

    /**
     * default action
     */
    public function index()
    {
        return $this->processor->index($this);
    }

    /**
     * default redirect after index action
     */
    public function show(array $data)
    {
        return view('antares/tester::admin.collective.show')->with($data);
    }

    /**
     * prepare modules info before run testing
     */
    public function prepare()
    {
        return $this->processor->prepare(Input::all());
    }

    /**
     * runs module configuration tests
     * 
     * @return \Illuminate\Http\Response
     */
    public function run()
    {
        return $this->processor->run($this, Input::get());
    }

    /**
     * @param View $view
     * 
     * @return String
     */
    public function render(View $view)
    {
        return $view->render();
    }

}
