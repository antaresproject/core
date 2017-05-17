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

namespace Antares\Brands\Http\Controllers\Admin;

use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Brands\Model\Brands as BrandModel;
use Antares\Brands\Listener\BrandTemplateing;
use Antares\Brands\Processor\Email;

class TemplateController extends AdminController implements BrandTemplateing
{

    /**
     * Define filters for current controller.
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');
        //$this->middleware('antares.can:brand-email', ['only' => ['update']]);
    }

    /**
     * on email branding
     * 
     * @param Email $processor
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function update(Email $processor, $id = null)
    {
        $id = is_null($id) ? brand_id() : $id;
        return $processor->email($this, $id);
    }

    /**
     * when validation dont give a shit
     * 
     * @param array $errors
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFailedValidation($errors, $id)
    {
        $url = extension_active('multibrand') ? "antares::brands/{$id}/email" : "antares::branding/email";
        return $this->redirectWithErrors(handles($url), $errors);
    }

    /**
     * when brand email update error
     * 
     * @param BrandModel $model
     * @param array $errors
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFailed(BrandModel $model, array $errors)
    {
        $message = trans('antares/brands::response.update.db-failed', $errors);
        $url     = extension_active('multibrand') ? "antares::brands/{$model->id}/email" : "antares::branding/email";
        return $this->redirectWithMessage(handles($url), $message, 'error');
    }

    /**
     * when brand email updated successfully
     * 
     * @param BrandModel $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updated($model)
    {
        $message = trans('antares/brands::response.update.success');
        $url     = extension_active('multibrand') ? "antares::multibrand/index" : "antares::branding/email";
        return $this->redirectWithMessage(handles($url), $message);
    }

}
