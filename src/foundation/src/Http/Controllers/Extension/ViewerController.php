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

namespace Antares\Foundation\Http\Controllers\Extension;

use Antares\Contracts\Html\Builder;
use Antares\Foundation\Processor\Extension\Viewer as Processor;
use Illuminate\Http\Request;
use URL;

class ViewerController extends Controller
{

    /**
     * Extensions Controller routing to manage available extensions.
     *
     * @param \Antares\Foundation\Processor\Extension\Viewer  $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();

        $this->processor = $processor;
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
        set_meta('title', trans('antares/foundation::title.components.list_breadcrumb'));

        return $this->processor->index();
    }

    /**
     * @param string $vendor
     * @param string $name
     * @return Builder
     */
    public function getConfiguration(string $vendor, string $name)
    {
        return $this->processor->showConfigurationForm($this, $vendor, $name);
    }

    /**
     * @param Builder $form
     * @return \Illuminate\Contracts\View\View
     */
    public function showConfigurationForm(Builder $form)
    {
        return view('antares/foundation::extensions.configure', compact('form'));
    }

    /**
     * @param string $customUrl
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToCustomUrl(string $customUrl)
    {
        return redirect()->to($customUrl);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function storeConfiguration(Request $request)
    {
        $componentId = $request->get('id');
        $options     = array_except($request->all(), ['id']);

        return $this->processor->updateConfiguration($this, $componentId, $options);
    }

    /**
     * Handles the failed validation for edited configuration.
     *
     * @param array $messages
     * @return mixed
     */
    public function updateConfigurationValidationFailed(array $messages)
    {
        if (request()->ajax()) {
            return response()->json($messages);
        }

        $url = URL::previous();

        return $this->redirectWithErrors($url, $messages);
    }

    /**
     * Handles the successfully updated configuration.
     *
     * @return mixed
     */
    public function updateConfigurationSuccess()
    {
        $url     = route(area() . '.modules.index');
        $message = trans('antares/foundation::response.extensions.configuration-success');

        return $this->redirectWithMessage($url, $message);
    }

    /**
     * Handles the failed update configuration.
     *
     * @param array $errors
     * @return mixed
     */
    public function updateConfigurationFailed(array $errors)
    {
        $url     = URL::previous();
        $message = trans('antares/foundation::response.extensions.configuration-failed');

        return $this->redirectWithMessage($url, $message, 'error');
    }

}
