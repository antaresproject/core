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
use Antares\Contracts\Extension\Listener\Configure as Listener;
use Antares\Foundation\Processor\Extension\ModuleConfigure as Processor;

class ModuleConfigureController extends Controller implements Listener
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
        $this->middleware('antares.can::module-configure', ['only' => ['configure', 'update'],]);
        $this->middleware('antares.can::module-create', ['only' => ['create', 'upload'],]);
    }

    /**
     * -----------------------------------CONFIGURE----------------------------------
     */

    /**
     * Configure module.
     *
     * GET (:antares)/modules/(:category)/(:name)/configure
     *
     * @param  string  $category
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return mixed
     */
    public function configure($category, $vendor, $package = null)
    {
        $this->processor->setCategory($category);
        $extension = $this->getExtension($vendor, $package);
        return $this->processor->configure($this, $extension);
    }

    /**
     * Update module configuration.
     *
     * POST (:antares)/modules/(:category)/(:name)/configure
     *
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return mixed
     */
    public function update($category = null, $vendor = null, $package = null)
    {
        $this->processor->setCategory($category);
        $extension = $this->getExtension($vendor, $package);
        return $this->processor->update($this, $extension, Input::all());
    }

    /**
     * Response for module configuration
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showConfigurationChanger(array $data)
    {
        $name = $data['extension']->name;
        set_meta('title', $name);
        set_meta('description', trans('antares/foundation::title.modules.configure'));

        return view('antares/foundation::modules.configure', $data);
    }

    /**
     * Response when update module configuration failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     * @param  string  $id
     *
     * @return mixed
     */
    public function updateConfigurationFailedValidation($errors, $id)
    {
        return $this->redirectWithErrors(handles("antares::modules/{$id}/configure"), $errors);
    }

    /**
     * Response when update module configuration succeed.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function configurationUpdated(Fluent $extension)
    {
        $category = $this->processor->resolveModuleCategoryName($extension);

        $message = trans('antares/foundation::response.modules.configure', $extension->getAttributes());

        return $this->redirectWithMessage(handles("antares::modules/{$category}"), $message);
    }

    /**
     * -----------------------------------CREATE----------------------------------
     */

    /**
     * create module.
     * 
     * GET (:antares)/modules/(:category)/create
     * 
     * @param String $category
     * @param String $vendor
     * @param String $package
     * @return mixed
     */
    public function create($category = null)
    {
        if (app('request')->isMethod('post')) {
            return $this->processor->extract($this, $category, Input::all());
        }
        return $this->processor->create($this, $category);
    }

    /**
     * Response for module creator
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showModuleCreator(array $data)
    {
        $namespace = 'antares/foundation::title.modules';
        $elements  = ['create'];
        if (isset($data['category']) && !empty($data['category'])) {
            array_push($elements, $data['category']);
        }
        $title = implode(': ', array_map(function($item) use($namespace) {
                    return trans($namespace . '.' . $item);
                }, $elements));

        set_meta('title', $title);
        return view('antares/foundation::modules.create', $data);
    }

    /**
     * retrive upload of module package
     * 

     * @param String $category
     * 
     * @return mixed
     */
    public function upload()
    {
        return $this->processor->upload(Input::all());
    }

    /**
     * Response when extraction of module succeed.
     *
     * @return mixed
     */
    public function moduleExtracted($category = null)
    {
        $message = trans('antares/foundation::response.modules.extracted-success');
        antares('memory')->getHandler()->forgetCache();
        app('antares.extension')->detect();

        return $this->redirectWithMessage(handles("antares::modules/{$category}"), $message);
    }

    /**
     * Response when extraction of module error.
     *
     * @return mixed
     */
    public function moduleExtractionError($category = null)
    {
        $message = trans('antares/foundation::response.modules.extracted-error');

        $this->redirectWithErrors(handles("antares::modules/{$category}"), [
            'message' => $message
        ]);
    }

}
