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

use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Input;
use Antares\Support\Facades\Foundation;
use Antares\Contracts\Extension\Listener\Configure as Listener;
use Antares\Foundation\Processor\Extension\Configure as Processor;

class ConfigureController extends Controller implements Listener
{

    /**
     * Extensions Controller routing to manage available extensions.
     *
     * @param  \Antares\Foundation\Processor\Extension\Configure  $processor
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
        $this->middleware('antares.can::configure-component', ['only' => ['configure', 'update'],]);
    }

    /**
     * Configure an extension.
     *
     * GET (:antares)/extensions/configure/(:name)
     *
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return mixed
     */
    public function configure($vendor, $package = null)
    {
        $extension = $this->getExtension($vendor, $package);
        return $this->processor->configure($this, $extension);
    }

    /**
     * Update extension configuration.
     *
     * POST (:antares)/extensions/configure/(:name)
     *
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return mixed
     */
    public function update($vendor, $package = null)
    {

        $extension = $this->getExtension($vendor, $package);
        return $this->processor->update($this, $extension, Input::all());
    }

    /**
     * Response for extension configuration.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showConfigurationChanger(array $data)
    {
        $name = $data['extension']->name;

        set_meta('title', Foundation::memory()->get("extensions.available.{$name}.name", $name));
        set_meta('description', trans('antares/foundation::title.extensions.configure'));

        return view('antares/foundation::extensions.configure', $data);
    }

    /**
     * Response when update extension configuration failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     * @param  string  $id
     *
     * @return mixed
     */
    public function updateConfigurationFailedValidation($errors, $id)
    {
        return $this->redirectWithErrors(handles("antares::extensions/{$id}/configure"), $errors);
    }

    /**
     * Response when update extension configuration succeed.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function configurationUpdated(Fluent $extension)
    {
        $message = trans('antares/foundation::response.extensions.configure', $extension->getAttributes());

        return $this->redirectWithMessage(handles('antares::extensions'), $message);
    }

}
