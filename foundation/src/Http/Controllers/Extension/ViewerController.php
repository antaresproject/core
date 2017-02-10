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


namespace Antares\Foundation\Http\Controllers\Extension;

use Antares\Foundation\Processor\Extension\Viewer as Processor;
use Antares\Contracts\Extension\Listener\Viewer as Listener;

class ViewerController extends Controller implements Listener
{

    /**
     * Extensions Controller routing to manage available extensions.
     *
     * @param \Antares\Foundation\Processor\Extension\Viewer  $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
        parent::__construct();
    }

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.manage');
    }

    /**
     * List all available extensions.
     *
     * GET (:antares)/extensions
     *
     * @return mixed
     */
    public function index()
    {

        app('antares.extension')->detect();
        set_meta('title', trans('antares/foundation::title.components.list_breadcrumb'));
        return $this->processor->index();
    }

    /**
     * Response for list of extensions viewer.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showExtensions(array $data)
    {
        set_meta('title', trans('antares/foundation::title.components.list_breadcrumb'));
        return view('antares/foundation::extensions.index', $data);
    }

}
