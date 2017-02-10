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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Brands\Http\Controllers\Admin;

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Brands\Processor\Brand as Processor;
use Antares\Brands\Model\Brands as BrandModel;
use Antares\Brands\Listener\BrandUpdater;
use Illuminate\Support\Facades\Input;
use Antares\Brands\Processor\Area;
use Antares\Brands\Processor\Logo;
use Illuminate\Http\Request;

class IndexController extends AdminController implements BrandUpdater
{

    /**
     *
     * @var Processor 
     */
    protected $processor;

    /**
     * CRUD Controller for Brands management using resource routing.
     * @param  \Antares\Multibrand\Processor\Brand  $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
        parent::__construct();
    }

    /**
     * Define filters for current controller.
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.forms:brand-update', ['only' => ['edit', 'update']]);
        //$this->middleware('antares.can:update-brand', ['only' => ['edit', 'update'],]);
    }

    /** ----------------------UPDATE ACTIONS---------------------- */

    /**
     * Edit brand.
     * @param  int  $id
     * @return mixed
     */
    public function edit($id)
    {
        return $this->processor->edit($this, $id);
    }

    /**
     * shows brand update form
     */
    public function showBrandChanger(array $data)
    {
        set_meta('title', trans('antares/brands::title.brands.update'));
        return view('antares/foundation::brands.edit', $data);
    }

    /**
     * when validation dont give a shit
     */
    public function updateBrandFailedValidation($errors, $id)
    {
        return $this->redirectWithErrors(handles("antares::brands/{$id}/edit"), $errors);
    }

    /**
     * when brand update error
     */
    public function updateBrandFailed(BrandModel $brand, array $errors)
    {
        $message = trans('antares/brands::response.update.db-failed', $errors);
        return $this->redirectWithMessage(handles("antares::brands/{$brand->id}/edit"), $message, 'error');
    }

    /**
     * when brand updated successfully
     */
    public function brandUpdated(BrandModel $brand)
    {
        $message = trans('antares/brands::response.update.success');
        $url     = extension_active('multibrand') ? "antares::multibrand/index" : "antares::brands/{$brand->id}/edit";
        return $this->redirectWithMessage(handles($url), $message);
    }

    /**
     * Update the brand.
     * 
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        return $this->processor->update($this, $id, $data);
    }

    /**
     * on upload brand logos
     * 
     * @param Logo $processor
     * @return \Illuminate\Http\Response
     */
    public function upload(Logo $processor)
    {
        return $processor->upload(Input::all());
    }

    /**
     * Brand settings per area
     * 
     * @param Area $processor
     * * @param mixed $brandId
     * @param mixed $templateId
     * @return \Illuminate\View\View
     */
    public function area(Area $processor, $brandId, $templateId)
    {
        return $processor->area($brandId, $templateId);
    }

    /**
     * when brand not exists
     */
    public function abortWhenBrandMismatched()
    {
        $message = trans('antares/brands::response.notexists');
        return $this->redirectWithMessage(handles('antares::foundation/'), $message);
    }

}
